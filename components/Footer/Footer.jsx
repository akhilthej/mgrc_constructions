import React from "react";
import Link from "next/link";

import { goodfirms, googlereview } from "../../public/icons";

import { FooterCompany } from "@/public/icons";

import { ImFacebook2, ImInstagram, ImTwitter, ImGithub } from "react-icons/im";
import Image from "next/image";

import { GlobalData } from "@/public/GlodalData";

const Footer = () => {
  const currentYear = new Date().getFullYear();
  return (
    <section className="bg-white w-full ">
      <footer>
        <div className="px-4 mx-auto sm:px-9 lg:px-8">
          <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div>
              <div className="text-black mb-2">
                <div className="leading-tight tracking-tighter">


                </div>
              </div>
              <Link href="/">
                <Image
                  src={FooterCompany}
                  alt="FooterCompany"
                  width="200"
                  height="200"
                />
              </Link>
              <p className="max-w-xs mt-4 text-[12px] text-black ">
                M G Rajeev & Co is a premier engineering and EPC contractor
                specializing in hydro-mechanical works, heavy steel structures,
                and marine infrastructure since 1984.
                <br />
              </p>

              <div className="flex space-x-6 text-black pt-2">
                <Link href={GlobalData.company.companyfacebook}>
                  <ImFacebook2 />
                </Link>
                <Link href={GlobalData.company.companyinstagram}>
                  <ImInstagram />
                </Link>
                <Link href={GlobalData.company.companytwitter}>
                  <ImTwitter />
                </Link>
                <Link href={GlobalData.company.companygit}>
                  <ImGithub />
                </Link>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-8 lg:col-span-2 lg:grid-cols-4">
              <div>
                <p className="font-medium text-black">Company</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <a href="/aboutus"> About us </a>
                  <a href="/projects"> Projects </a>
                </nav>
              </div>
              <div>
                <p className="font-medium text-black">Services</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <a href="/capabilities"> Our Capabilities </a>
                  <a href={GlobalData.company.companyWhatsapp}> Support </a>
                </nav>
              </div>
              <div>
                <p className="font-medium text-black">Helpful Links</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <a href="/contactus"> Contact </a>
                  <a href="/contactus"> Inquiries </a>
                  <a href={GlobalData.company.companyWhatsapp}> Live Chat </a>
                </nav>
              </div>
              <div>
                <p className="font-medium text-black">Legal</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <Link href="/contactus"> Privacy Policy </Link>
                  <Link href="/contactus"> Terms & Conditions </Link>
                </nav>
              </div>
            </div>
          </div>

          <div className="h-px my-8 border-0 bg-gray-700" />
          <p
            id="copyright"
            className="cursor-default text-center text-[12px] text-black"
          >
            © 1984-<span> {currentYear} </span>
            <span className="font-bold">M G Rajeev & Co.</span> All Rights
            Reserved.
          </p>
          <div className="flex pt-4 justify-center text-black text-[12px] space-x-4">
            <a href="/contactus">Privacy Policy</a>
            <a href="/contactus">Terms & Conditions</a>
          </div>
        </div>
      </footer>
      <section />
    </section>
  );
};

export default Footer;
