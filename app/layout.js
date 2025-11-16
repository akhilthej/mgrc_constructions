// app/layout.js
import { Inter } from "next/font/google";
import "./globals.css";
import Navbar from "@/components/Navbar/Navbar";
import Footer from "@/components/Footer/Footer";

const inter = Inter({ subsets: ["latin"] });

export const metadata = {
  icons: {
    icon: ["/favicon/favicon.png"],
    apple: ["/favicon/apple-touch-icon.png"],
    shortcut: ["/favicon/apple-touch-icon.png"],
  },
  manifest: "/favicon/site.webmanifest",
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <Navbar />
          {children}
            <Footer />
      </body>
    </html>
  );
}
