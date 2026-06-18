"use client";

import { useState, useEffect } from "react";
import api from "@/lib/axios";
import { toast } from "react-hot-toast";
import { useQueryClient } from "@tanstack/react-query";
import { motion, AnimatePresence } from "framer-motion";
import { Loader2, X } from "lucide-react";
import { useAuthStore } from "@/stores/auth-store";

export function AuthModal() {
  const { 
    isAuthenticated, setIsAuthenticated, 
    isAuthModalOpen: isOpen, 
    openAuthModal: setIsOpenTrue, 
    closeAuthModal: setIsOpenFalse 
  } = useAuthStore();
  const queryClient = useQueryClient();
  
  const setIsOpen = (val: boolean) => val ? setIsOpenTrue() : setIsOpenFalse();
  
  const [isLogin, setIsLogin] = useState(true);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    // Sync state on mount just in case
    setIsAuthenticated(!!localStorage.getItem('access_token'));
  }, [setIsAuthenticated]);

  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const handleLogout = async () => {
    try {
      await api.post('/auth/logout');
    } catch (error) {
      console.error(error);
    } finally {
      localStorage.removeItem('access_token');
      queryClient.clear(); // Bersihkan data cache (favorites, dll) secara instan tanpa reload
      setIsAuthenticated(false);
      toast.success("Sesi Diakhiri. Anda telah Logout.");
    }
  };

  if (isAuthenticated) {
    return (
      <button
        onClick={handleLogout}
        className="glass-panel hard-shadow px-4 h-12 flex items-center text-muted-foreground hover:text-destructive font-mono-data text-xs uppercase tracking-widest transition-colors"
      >
        [ LOGOUT ]
      </button>
    );
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      // Fetch CSRF cookie from Laravel to bypass CSRF token mismatch error
      const baseUrl = process.env.NEXT_PUBLIC_API_BASE_URL?.replace('/api/v1', '') || 'http://localhost:8000';
      await api.get(`${baseUrl}/sanctum/csrf-cookie`);

      if (isLogin) {
        const res = await api.post('/auth/login', {
          email: form.email,
          password: form.password,
        });
        const token = res.data.data.access_token;
        localStorage.setItem('access_token', token);
        setIsAuthenticated(true);
        setIsOpen(false);
        toast.success("Login Successful. Sistem Online.");
      } else {
        const res = await api.post('/auth/register', form);
        const token = res.data.data.access_token;
        localStorage.setItem('access_token', token);
        setIsAuthenticated(true);
        setIsOpen(false);
        toast.success("Registrasi Berhasil. Akses Diberikan.");
      }
    } catch (error: any) {
      const data = error.response?.data;
      if (data?.errors) {
        // Tampilkan semua error validasi
        Object.values(data.errors).forEach((errMsgs: any) => {
          errMsgs.forEach((msg: string) => toast.error(`[VALIDATION_ERR] ${msg}`));
        });
      } else {
        toast.error(data?.message || "Koneksi ke server gagal.");
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      {/* Trigger Button if closed */}
      {!isOpen && (
        <button
          onClick={() => setIsOpen(true)}
          className="glass-panel hard-shadow px-6 h-12 flex items-center text-primary font-heading uppercase tracking-widest text-sm hover:bg-white/10 transition-colors"
        >
          [ LOGIN ]
        </button>
      )}

      {/* Modal */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
          >
            <motion.div
              initial={{ scale: 0.95, y: 20 }}
              animate={{ scale: 1, y: 0 }}
              exit={{ scale: 0.95, y: 20 }}
              className="glass-panel w-full max-w-md border-2 border-border hard-shadow p-6 relative"
            >
              <button 
                onClick={() => setIsOpen(false)}
                className="absolute top-4 right-4 text-muted-foreground hover:text-destructive transition-colors"
                type="button"
              >
                <X className="w-5 h-5" />
              </button>
              
              <h2 className="text-2xl font-heading font-bold text-primary uppercase tracking-wider mb-6 dither-pattern pb-4 border-b border-border">
                {isLogin ? "AeroCast // Auth" : "AeroCast // Register"}
              </h2>

              <form onSubmit={handleSubmit} className="space-y-4">
                {!isLogin && (
                  <div>
                    <label className="block font-mono-data text-xs text-muted-foreground uppercase mb-1">Name</label>
                    <input
                      type="text"
                      required
                      className="w-full bg-black/50 border border-border text-foreground px-4 py-2 focus:outline-none focus:border-primary font-mono-data text-sm"
                      value={form.name}
                      onChange={e => setForm({ ...form, name: e.target.value })}
                    />
                  </div>
                )}
                <div>
                  <label className="block font-mono-data text-xs text-muted-foreground uppercase mb-1">Email</label>
                  <input
                    type="email"
                    required
                    className="w-full bg-black/50 border border-border text-foreground px-4 py-2 focus:outline-none focus:border-primary font-mono-data text-sm"
                    value={form.email}
                    onChange={e => setForm({ ...form, email: e.target.value })}
                  />
                </div>
                <div>
                  <label className="block font-mono-data text-xs text-muted-foreground uppercase mb-1">Password</label>
                  <input
                    type="password"
                    required
                    className="w-full bg-black/50 border border-border text-foreground px-4 py-2 focus:outline-none focus:border-primary font-mono-data text-sm"
                    value={form.password}
                    onChange={e => setForm({ ...form, password: e.target.value })}
                  />
                </div>
                {!isLogin && (
                  <div>
                    <label className="block font-mono-data text-xs text-muted-foreground uppercase mb-1">Confirm Password</label>
                    <input
                      type="password"
                      required
                      className="w-full bg-black/50 border border-border text-foreground px-4 py-2 focus:outline-none focus:border-primary font-mono-data text-sm"
                      value={form.password_confirmation}
                      onChange={e => setForm({ ...form, password_confirmation: e.target.value })}
                    />
                  </div>
                )}

                <button
                  type="submit"
                  disabled={isLoading}
                  className="w-full bg-primary text-primary-foreground font-heading uppercase py-3 mt-4 hard-shadow hover:bg-primary/90 transition-colors flex justify-center items-center gap-2"
                >
                  {isLoading ? <Loader2 className="w-5 h-5 animate-spin" /> : (isLogin ? "System Login" : "Initialize Account")}
                </button>
              </form>

              <div className="mt-6 text-center">
                <button
                  type="button"
                  onClick={() => setIsLogin(!isLogin)}
                  className="font-mono-data text-xs text-muted-foreground hover:text-primary transition-colors uppercase"
                >
                  {isLogin ? ">> Create new personnel profile" : "<< Return to login"}
                </button>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
