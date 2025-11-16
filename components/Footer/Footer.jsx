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
                We are a web Developing & Designing company with a mission to
                help build there business online. We accomplish this by
                continuously developing technology, giving expert assistance,
                and ensuring a flawless online website experience.
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
                  <a href="/clients"> Clients </a>
                </nav>
              </div>
              <div>
                <p className="font-medium text-black">Services</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <a href="https://search.google.com/local/writereview?placeid=ChIJM0IEPqRRmg0RyLQAP_5varc">
                    {" "}
                    Google Map Review{" "}
                  </a>
                  <a href="/reviews"> Client Review </a>
                </nav>
              </div>
              <div>
                <p className="font-medium text-black">Helpful Links</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <a href="/contactus"> Contact </a>
                  <a href="/contactus"> FAQs </a>
                  <a href={GlobalData.company.companyWhatsapp}> Live Chat </a>
                </nav>
              </div>
              <div>
                <p className="font-medium text-black">Legal</p>
                <nav className="flex flex-col mt-4 space-y-2 text-[12px] text-black">
                  <Link href="/Legal/privacy-policy"> Privacy Policy </Link>
                  <Link href="/Legal/terms-and-conditions">
                    <div> Terms & Conditions </div>
                  </Link>

                  <Link href="/Legal/return-policy">
                    <div> Cancellation & Refund Policy </div>
                  </Link>

                  <Link href="/Legal/disclaimer">
                    <div> Disclaimer </div>
                  </Link>

                  <Link href="/Legal/shipping-and-delivery">
                    <div> Shipping & Delivery </div>
                  </Link>
                </nav>
              </div>
            </div>
          </div>

          <div className="flex items-center justify-center">
            <div
              className="p-2"
              href="https://www.goodfirms.co/company/cyber-space-digital"
            >
              <Image
                className="h-10 rounded-sm hover:animate-pulse max-w-full"
                src={goodfirms}
                alt="goodfirms_icon"
                width="120"
                height="50"
              />
            </div>
            <div className="p-2" href="https://g.page/r/Cci0AD_-b2q3EAI/review">
              <Image
                className="h-10 hover:animate-pulse max-w-full"
                src={googlereview}
                alt="googlereview_icon"
                width="120"
                height="50"
              />
            </div>
          </div>

          <div className="h-px my-8 border-0 bg-gray-700" />
          <p
            id="copyright"
            className="cursor-default text-center text-[12px] text-black"
          >
            © 2020-<span> {currentYear} </span>
            <span className="font-bold">Cyber Space Digital.</span> All Rights
            Reserved.
            <br />A Development & Designer Community ( #CSD )
          </p>
          <div className="flex pt-4 justify-center text-black text-[12px] space-x-4">
            <a href="/Legal/privacy-policy">Privacy Policy</a>
            <a href="/Legal/terms-and-conditions">Terms & Conditions</a>
            <a href="/Legal/disclaimer">Disclaimer</a>
            <a href="/site-map">Site Map</a>
          </div>
        </div>
      </footer>
      <section />
    </section>
  );
};

export default Footer;
