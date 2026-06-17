import type { Metadata } from "next";
import { Inter, Space_Grotesk } from "next/font/google";
import QueryProvider from "@/components/providers/query-provider";
import { Toaster } from "react-hot-toast";
import "./globals.css";
import "mapbox-gl/dist/mapbox-gl.css";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-sans",
});

const spaceGrotesk = Space_Grotesk({
  subsets: ["latin"],
  variable: "--font-heading",
});

const spaceGroteskMono = Space_Grotesk({
  subsets: ["latin"],
  variable: "--font-mono-data",
  weight: ["500", "600"],
});

export const metadata: Metadata = {
  title: "AeroCast - Weather & Environment Dashboard",
  description: "AeroCast is a weather dashboard with an interactive 2D map.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  // Selalu force dark mode karena tema default adalah dark
  return (
    <html
      lang="en"
      className={`${inter.variable} ${spaceGrotesk.variable} ${spaceGroteskMono.variable} dark antialiased h-full`}
    >
      <body className="min-h-full flex flex-col">
        <QueryProvider>
          {children}
          <Toaster position="bottom-center" />
        </QueryProvider>
      </body>
    </html>
  );
}
