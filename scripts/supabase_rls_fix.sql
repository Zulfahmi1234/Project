-- =============================================================================
-- AeroCast — Supabase RLS Security Fix
-- =============================================================================
-- Resolves all Supabase database linter warnings:
--   • rls_disabled_in_public  (0013) — all 11 affected tables
--   • sensitive_columns_exposed (0023) — password_reset_tokens, personal_access_tokens, users
--
-- STRATEGY:
--   Laravel internal tables (migrations, cache, cache_locks, jobs, job_batches,
--   failed_jobs, sessions, password_reset_tokens, personal_access_tokens)
--     → Enable RLS with NO permissive policies  (effectively blocks all PostgREST access)
--     → Revoke SELECT from anon & authenticated roles for extra safety
--
--   Application tables (users, favorite_locations)
--     → Enable RLS with sensible user-scoped policies
--
-- Run this in: Supabase Dashboard → SQL Editor
-- =============================================================================


-- -----------------------------------------------------------------------------
-- 1. LARAVEL INTERNAL TABLES
--    These tables are used exclusively by the Laravel backend via the postgres
--    superuser connection and must NEVER be readable via PostgREST / the API.
-- -----------------------------------------------------------------------------

-- 1a. Enable RLS (an empty policy set = deny all via PostgREST)
ALTER TABLE public.migrations             ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.cache                  ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.cache_locks            ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.jobs                   ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.job_batches            ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.failed_jobs            ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.sessions               ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.password_reset_tokens  ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.personal_access_tokens ENABLE ROW LEVEL SECURITY;

-- 1b. Revoke all PostgREST roles from these internal tables
--     (belt-and-suspenders on top of RLS)
REVOKE ALL ON public.migrations             FROM anon, authenticated;
REVOKE ALL ON public.cache                  FROM anon, authenticated;
REVOKE ALL ON public.cache_locks            FROM anon, authenticated;
REVOKE ALL ON public.jobs                   FROM anon, authenticated;
REVOKE ALL ON public.job_batches            FROM anon, authenticated;
REVOKE ALL ON public.failed_jobs            FROM anon, authenticated;
REVOKE ALL ON public.sessions               FROM anon, authenticated;
REVOKE ALL ON public.password_reset_tokens  FROM anon, authenticated;
REVOKE ALL ON public.personal_access_tokens FROM anon, authenticated;


-- -----------------------------------------------------------------------------
-- 2. USERS TABLE
--    Authenticated users may only read/update their own row.
--    Only the backend (postgres role) can insert or delete.
-- -----------------------------------------------------------------------------

ALTER TABLE public.users ENABLE ROW LEVEL SECURITY;

-- Drop existing policies before recreating (idempotent re-run)
DROP POLICY IF EXISTS "users_select_own"  ON public.users;
DROP POLICY IF EXISTS "users_update_own"  ON public.users;

-- Allow a user to read their own record
CREATE POLICY "users_select_own"
    ON public.users
    FOR SELECT
    TO authenticated
    USING (auth.uid()::text = id::text);

-- Allow a user to update their own record
CREATE POLICY "users_update_own"
    ON public.users
    FOR UPDATE
    TO authenticated
    USING (auth.uid()::text = id::text)
    WITH CHECK (auth.uid()::text = id::text);

-- Revoke direct anon access (no unauthenticated user should see the users table)
REVOKE ALL ON public.users FROM anon;


-- -----------------------------------------------------------------------------
-- 3. FAVORITE_LOCATIONS TABLE
--    Users may only access their own favorite locations.
-- -----------------------------------------------------------------------------

ALTER TABLE public.favorite_locations ENABLE ROW LEVEL SECURITY;

-- Drop existing policies before recreating (idempotent re-run)
DROP POLICY IF EXISTS "favorite_locations_select_own"  ON public.favorite_locations;
DROP POLICY IF EXISTS "favorite_locations_insert_own"  ON public.favorite_locations;
DROP POLICY IF EXISTS "favorite_locations_update_own"  ON public.favorite_locations;
DROP POLICY IF EXISTS "favorite_locations_delete_own"  ON public.favorite_locations;

-- SELECT: only own rows
CREATE POLICY "favorite_locations_select_own"
    ON public.favorite_locations
    FOR SELECT
    TO authenticated
    USING (auth.uid()::text = user_id::text);

-- INSERT: only for own user_id
CREATE POLICY "favorite_locations_insert_own"
    ON public.favorite_locations
    FOR INSERT
    TO authenticated
    WITH CHECK (auth.uid()::text = user_id::text);

-- UPDATE: only own rows
CREATE POLICY "favorite_locations_update_own"
    ON public.favorite_locations
    FOR UPDATE
    TO authenticated
    USING (auth.uid()::text = user_id::text)
    WITH CHECK (auth.uid()::text = user_id::text);

-- DELETE: only own rows
CREATE POLICY "favorite_locations_delete_own"
    ON public.favorite_locations
    FOR DELETE
    TO authenticated
    USING (auth.uid()::text = user_id::text);

-- Revoke anon access
REVOKE ALL ON public.favorite_locations FROM anon;


-- =============================================================================
-- Verification queries — run these after applying to confirm the fix
-- =============================================================================
--
-- Check RLS is enabled on all affected tables:
--   SELECT tablename, rowsecurity
--   FROM pg_tables
--   WHERE schemaname = 'public'
--   ORDER BY tablename;
--
-- List all RLS policies:
--   SELECT schemaname, tablename, policyname, roles, cmd, qual
--   FROM pg_policies
--   WHERE schemaname = 'public'
--   ORDER BY tablename, policyname;
--
-- =============================================================================
