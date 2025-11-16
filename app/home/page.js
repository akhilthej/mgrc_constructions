"use client";
import React from "react";
import Image from "next/image";
import {
  HeroSteel,
  HydroDam,
  NavalDockyard,
  SteelPlant,
} from "@/public/images.jsx";

function Page() {
  return (
    <>
      {/* Hero Section */}
      <section className="min-h-screen flex items-center relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
        <div className="absolute inset-0 bg-grid-white/5" />
        <div className="absolute top-20 right-20 w-96 h-96 bg-blue-600/20 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow" />
        <div className="absolute bottom-20 left-20 w-96 h-96 bg-amber-500/20 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow" />
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid lg:grid-cols-2 gap-16 items-center">
            {/* Hero Content */}
            <div className="hero-content text-white">
              <div className="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm border border-white/20 text-white px-6 py-3 rounded-full text-sm font-semibold mb-8">
                <div className="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                40+ Years of Engineering Excellence
              </div>
              
              <h1 className="font-bold text-4xl md:text-6xl lg:text-7xl mb-8 leading-tight">
                Building India's
                <span className="block text-amber-400 mt-2">Infrastructure</span>
                With Precision & Trust
              </h1>
              
              <p className="text-xl text-blue-100 mb-10 leading-relaxed max-w-2xl">
                Premier engineering and EPC contractor specializing in hydro-mechanical works, 
                heavy steel structures, and marine infrastructure for defence and industrial sectors.
              </p>
              
              <div className="flex flex-col sm:flex-row gap-5 mb-12">
                <a
                  className="group bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105 text-center shadow-lg hover:shadow-xl border-2 border-amber-500"
                  href="/contact"
                >
                  <span className="flex items-center justify-center gap-3">
                    Start Your Project
                    <svg className="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                  </span>
                </a>
                <a
                  className="group bg-transparent border-2 border-white text-white hover:bg-white hover:text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 text-center"
                  href="#company-profile"
                >
                  <span className="flex items-center justify-center gap-3">
                    Download Profile
                    <svg className="w-5 h-5 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </span>
                </a>
              </div>

              {/* Trust Badges */}
              <div className="flex flex-wrap items-center gap-8 text-sm text-blue-200">
                <div className="flex items-center gap-2">
                  <div className="w-2 h-2 bg-green-400 rounded-full"></div>
                  ISO 9001:2008 Certified
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-2 h-2 bg-green-400 rounded-full"></div>
                  Registered with Indian Navy
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-2 h-2 bg-green-400 rounded-full"></div>
                  200+ Projects Completed
                </div>
              </div>
            </div>
            
            {/* Hero Image */}
            <div className="hidden lg:block relative">
              <div className="relative rounded-2xl overflow-hidden shadow-2xl border-2 border-white/20">
                <Image
                  src={HeroSteel}
                  alt="Heavy Steel Structure Fabrication"
                  className="w-full h-auto"
                  priority
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent" />
                
                {/* Image Badge */}
                <div className="absolute bottom-6 left-6 bg-white/90 backdrop-blur-sm rounded-lg p-4 max-w-xs">
                  <h4 className="font-bold text-slate-900 text-sm mb-1">Heavy Structural Fabrication</h4>
                  <p className="text-slate-600 text-xs">Precision engineering for industrial applications</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Company Snapshot */}
      <section className="py-24 bg-white relative overflow-hidden">
        <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-blue-500"></div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Company <span className="text-blue-600">Snapshot</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Established in 1984, M G Rajeev & Co has built a reputation for excellence 
              in engineering and construction across diverse sectors.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              {
                icon: "M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 011.788 0l4 1.714a.999.999 0 01.356.257l5.25 2.041a1 1 0 00.788 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z",
                title: "Established",
                value: "1984",
                description: "Four decades of excellence",
                color: "text-blue-600"
              },
              {
                icon: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z",
                title: "Certification",
                value: "ISO 9001:2008",
                description: "Quality Management System",
                color: "text-amber-600"
              },
              {
                icon: "M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z",
                title: "Head Office",
                value: "Visakhapatnam",
                description: "Andhra Pradesh, India",
                color: "text-blue-600"
              },
              {
                icon: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4",
                title: "Legal Status",
                value: "Partnership Firm",
                description: "Family owned & operated",
                color: "text-amber-600"
              }
            ].map((item, index) => (
              <div
                key={index}
                className="text-center p-8 bg-slate-50 rounded-xl border border-slate-200 hover:shadow-lg transition-all duration-300"
              >
                <div className={`inline-flex items-center justify-center w-16 h-16 bg-white rounded-xl mb-6 border border-slate-200`}>
                  <svg className={`w-8 h-8 ${item.color}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={item.icon} />
                  </svg>
                </div>
                <h3 className="text-2xl font-bold text-slate-900 mb-2">{item.title}</h3>
                <p className="text-slate-600 text-lg font-semibold mb-2">{item.value}</p>
                <p className="text-sm text-slate-500">{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Core Capabilities */}
      <section className="py-24 bg-slate-50 relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-white via-slate-50 to-blue-50"></div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Core <span className="text-blue-600">Capabilities</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Comprehensive engineering and construction services across diverse sectors 
              with uncompromising quality and safety standards.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[
              {
                icon: "M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z",
                title: "Hydro-mechanical Works",
                description: "Fabrication, erection, testing & commissioning of penstocks, radial/vertical gates, gantries, and hoists.",
                features: ["Penstocks and pressure shafts", "Radial and vertical gates", "Gantry cranes and hoists"],
                color: "text-blue-600"
              },
              {
                icon: "M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10",
                title: "Heavy Structural Fabrication",
                description: "Heavy structural steel fabrication and installation for industrial, marine, and infrastructure projects.",
                features: ["Industrial steel structures", "Marine and offshore structures", "Bridge components"],
                color: "text-amber-600"
              },
              {
                icon: "M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z",
                title: "Marine & Dockyard Works",
                description: "Specialized marine construction including caisson gates, dock blocks, and naval infrastructure.",
                features: ["Caisson gates and dock blocks", "Slipway upgrades", "Naval dockyard infrastructure"],
                color: "text-blue-600"
              },
              {
                icon: "M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z",
                title: "Surface Preparation & Coatings",
                description: "Professional surface preparation and protective coatings using advanced techniques.",
                features: ["Hydro and abrasive blasting", "Airless spray painting", "Protective coating systems"],
                color: "text-amber-600"
              },
              {
                icon: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4",
                title: "Civil Engineering Works",
                description: "Comprehensive civil engineering services including repairs and infrastructure development.",
                features: ["Building repairs and upgradation", "Roofing and cladding systems", "Infrastructure development"],
                color: "text-blue-600"
              },
              {
                icon: "M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z",
                title: "Electrical & Mechanical Works",
                description: "Complete electrical and mechanical solutions for industrial applications.",
                features: ["Motor and pump installations", "Crane and hoist systems", "Control panel fabrication"],
                color: "text-amber-600"
              }
            ].map((capability, index) => (
              <div
                key={index}
                className="bg-white rounded-xl p-8 border border-slate-200 hover:shadow-lg transition-all duration-300 group"
              >
                <div className={`inline-flex items-center justify-center w-14 h-14 bg-slate-50 rounded-xl mb-6 group-hover:bg-${capability.color.split('-')[1]}-50 border border-slate-200`}>
                  <svg className={`w-7 h-7 ${capability.color}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={capability.icon} />
                  </svg>
                </div>
                <h3 className="text-2xl font-bold text-slate-900 mb-4">{capability.title}</h3>
                <p className="text-slate-600 mb-6 leading-relaxed">{capability.description}</p>
                <ul className="space-y-3">
                  {capability.features.map((feature, featureIndex) => (
                    <li key={featureIndex} className="flex items-center gap-3 text-slate-700">
                      <div className={`w-1.5 h-1.5 rounded-full ${capability.color.replace('text', 'bg')}`}></div>
                      {feature}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Projects */}
      <section className="py-24 bg-slate-900 relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-white/5"></div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Featured <span className="text-amber-400">Projects</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
              Showcasing our expertise through landmark projects across defence, 
              hydro power, and industrial sectors.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <ProjectCard
              image={HydroDam}
              title="Tehri Dam Hydro-mechanical Works"
              category="Hydro Power"
              location="Uttarakhand"
              client="THDC Ltd"
              description="Complete hydro-mechanical works including penstocks, radial gates, gantry cranes, and hoists."
            />
            <ProjectCard
              image={NavalDockyard}
              title="Naval Dockyard Marine Infrastructure"
              category="Marine"
              location="Visakhapatnam"
              client="Indian Navy"
              description="Construction of caisson gates, dock blocks, slipway upgrades, and marine structures."
            />
            <ProjectCard
              image={SteelPlant}
              title="Integrated Mini Steel Plant"
              category="Industrial"
              location="Kothavalasa"
              client="Maa Maha Maya Industries"
              description="Complete fabrication, erection, testing and commissioning of integrated steel plant."
            />
          </div>

          <div className="text-center mt-16">
            <a
              href="/projects"
              className="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              View All Projects
              <svg className="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
          </div>
        </div>
      </section>

      {/* Sectors Served */}
      <section className="py-24 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Sectors <span className="text-blue-600">Served</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Trusted partner for premier government and private clients across 
              defence, hydro power, heavy industry, and public works sectors.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            {[
              {
                icon: "M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222",
                title: "Indian Navy",
                description: "Strategic defence infrastructure including dockyards and naval base facilities.",
                color: "text-blue-600"
              },
              {
                icon: "M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z",
                title: "Hydro Power",
                description: "Complete hydro-mechanical systems for dams and power plants.",
                color: "text-amber-600"
              },
              {
                icon: "M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10",
                title: "Heavy Industry",
                description: "Steel plants and industrial infrastructure with heavy structural requirements.",
                color: "text-blue-600"
              },
              {
                icon: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4",
                title: "Public Works",
                description: "Government infrastructure projects and public facilities.",
                color: "text-amber-600"
              }
            ].map((sector, index) => (
              <div
                key={index}
                className="text-center p-8 bg-slate-50 rounded-xl border border-slate-200 hover:shadow-lg transition-all duration-300"
              >
                <div className={`inline-flex items-center justify-center w-16 h-16 bg-white rounded-xl mb-6 mx-auto border border-slate-200`}>
                  <svg className={`w-8 h-8 ${sector.color}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={sector.icon} />
                  </svg>
                </div>
                <h3 className="text-2xl font-bold text-slate-900 mb-4">{sector.title}</h3>
                <p className="text-slate-600 leading-relaxed">{sector.description}</p>
              </div>
            ))}
          </div>

          {/* Clients */}
          <div className="bg-slate-50 rounded-2xl p-12 border border-slate-200">
            <h3 className="text-3xl font-bold text-center mb-12 text-slate-900">
              Registered With & Executed Works For
            </h3>
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-center">
              {["DGNP(V)", "Naval Dockyard", "Ship Building Centre", "THDC Ltd", "Odisha Construction Corp", "And More..."].map((org, index) => (
                <div key={index} className="bg-white text-slate-700 px-4 py-4 rounded-xl font-semibold border border-slate-200 hover:shadow-md transition-all duration-300">
                  {org}
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24 bg-slate-900 relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-white/5"></div>
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h2 className="text-4xl md:text-5xl font-bold text-white mb-8">
            Ready to Build Your Next Project?
          </h2>
          <p className="text-xl text-slate-300 mb-12 max-w-3xl mx-auto leading-relaxed">
            Partner with 40+ years of engineering excellence. Let's discuss your 
            infrastructure needs and create something remarkable together.
          </p>

          <div className="flex flex-col sm:flex-row gap-6 justify-center">
            <a
              href="/contact"
              className="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              Get In Touch
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </a>
            <a
              href="/capabilities"
              className="inline-flex items-center gap-3 bg-transparent border-2 border-white text-white hover:bg-white hover:text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300"
            >
              Our Services
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </a>
          </div>
        </div>
      </section>
    </>
  );
}

// Updated Project Card Component
const ProjectCard = ({
  image,
  title,
  category,
  location,
  client,
  description
}) => (
  <div className="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group">
    <div className="relative w-full h-48 overflow-hidden">
      <Image 
        src={image} 
        alt={title} 
        fill 
        className="object-cover group-hover:scale-105 transition-transform duration-500" 
      />
      <div className="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent" />
      <div className="absolute top-4 left-4">
        <span className="bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-medium">
          {category}
        </span>
      </div>
      <div className="absolute top-4 right-4 text-white text-sm bg-black/30 px-3 py-1 rounded-lg backdrop-blur-sm">
        {location}
      </div>
    </div>
    <div className="p-6">
      <h3 className="text-xl font-bold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">
        {title}
      </h3>
      <p className="text-slate-600 mb-4 leading-relaxed">{description}</p>
      <div className="flex items-center justify-between text-sm pt-4 border-t border-slate-200">
        <span className="text-slate-700 font-semibold">Client: {client}</span>
        <div className="flex items-center gap-1 text-amber-600 font-semibold">
          <span>View Details</span>
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
          </svg>
        </div>
      </div>
    </div>
  </div>
);

export default Page;