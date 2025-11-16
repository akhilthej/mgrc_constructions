"use client";
import { useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { TiThMenu } from "react-icons/ti";
import { CompanyNavLogo } from "@/public/icons";

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);

  const navLinks = [
    { title: "Home", href: "/" },
    { title: "About", href: "/aboutus" },
    { title: "Capabilities", href: "/capabilities" },
    { title: "Projects", href: "/projects" },
    { title: "Contact", href: "/contactus" },
  ];
  return (
    <section className="w-full top-0   z-[9999]">
      <div className="flex justify-between items-center px-4 md:px-8 h-16">
        <div>
          <Link href="/">
            <Image src={CompanyNavLogo} alt="Logo" className="h-10 w-auto" />
          </Link>
        </div>

        <div>
          <ul className="hidden sm:flex gap-6 items-center">
            {navLinks.map(({ title, href }) => (
              <li key={href}>
                <Link
                  href={href}
                  className="text-[13px] text-black hover:text-yellow-500 transition"
                >
                  {title}
                </Link>
              </li>
            ))}
          </ul>

          <TiThMenu
            onClick={() => setIsOpen(true)}
            className="text-black text-3xl block sm:hidden cursor-pointer"
          />
        </div>
      </div>

      {/* Fullscreen Overlay Menu */}
      <div
        className={`fixed z-[100] top-0 right-0 w-full h-full bg-black text-white transform transition-transform duration-500 ${
          isOpen ? "translate-x-0" : "translate-x-full"
        }`}
      >
        <div className=" flex flex-col justify-center items-center h-full space-y-8 text-2xl">
          <button
            className="absolute top-6 right-6 text-white text-3xl"
            onClick={() => setIsOpen(false)}
          >
            &times;
          </button>
          <Link
            href="/services/web-development-company"
            onClick={() => setIsOpen(false)}
          >
            WEB DEVELOPMENT
          </Link>
          <Link
            href="/services/mobile-app-development-company"
            onClick={() => setIsOpen(false)}
          >
            Mobile App
          </Link>
          <Link href="/aboutus" onClick={() => setIsOpen(false)}>
            About Us
          </Link>
          <Link href="/contactus" onClick={() => setIsOpen(false)}>
            Contact
          </Link>
        </div>
      </div>
    </section>
  );
};

export default Navbar;
