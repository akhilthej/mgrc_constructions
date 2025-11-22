"use client";
import React, { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { 
  FaEye, 
  FaHeart,
  FaAward,
  FaShieldAlt,
  FaShip,
  FaCogs,
  FaCertificate,
  FaUsers,
  FaRocket,
  FaBuilding,
  FaIndustry,
  FaWater,
  FaHardHat,
  FaToolbox,
  FaCheckCircle,
  FaChartLine,
  FaBullseye,
  FaHistory,
  FaRegClock
} from "react-icons/fa";
import { 
  HiOutlineLightningBolt,
  HiOutlineCog,
  HiOutlineUserGroup,
  HiOutlineCalendar
} from "react-icons/hi";

function AboutUs() {
  const [yearsCount, setYearsCount] = useState(0);
  const [projectsCount, setProjectsCount] = useState(0);

  useEffect(() => {
    // Animate counters
    const animateCounter = (setter, target, duration = 2000) => {
      let start = 0;
      const increment = target / (duration / 16);
      const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
          setter(target);
          clearInterval(timer);
        } else {
          setter(Math.floor(start));
        }
      }, 16);
    };

    animateCounter(setYearsCount, 40);
    animateCounter(setProjectsCount, 500);
  }, []);

  const leadership = [
    {
      initials: "SR",
      name: "S. Rukmani",
      role: "Managing Partner",
      description: "Strategic leadership and business development with focus on client relationships and organizational growth.",
      gradient: "from-blue-500 to-cyan-500",
      icon: <FaUsers className="w-6 h-6" />
    },
    {
      initials: "RGN",
      name: "R. Goutham Nair",
      role: "Director Projects",
      description: "Project management and technical oversight with expertise in complex engineering execution and delivery.",
      gradient: "from-amber-500 to-orange-500",
      icon: <FaHardHat className="w-6 h-6" />
    },
    {
      initials: "RSN",
      name: "R Sajana Nair",
      role: "Partner & Finance",
      description: "Financial management and administrative oversight ensuring operational efficiency and compliance.",
      gradient: "from-purple-500 to-pink-500",
      icon: <FaChartLine className="w-6 h-6" />
    },
    {
      initials: "PPN",
      name: "P Padma Nair",
      role: "Administration Head",
      description: "Human resources and administrative operations with focus on team development and organizational excellence.",
      gradient: "from-green-500 to-emerald-500",
      icon: <HiOutlineUserGroup className="w-6 h-6" />
    }
  ];

  const technicalTeam = [
    {
      initials: "HNR",
      name: "H. Nageswara Rao",
      role: "Chief Project Manager",
      description: "B.Sc. Engg (NIT Rourkela); ex-HSL Hull Shop Head with expertise in shipbuilding and complex project delivery.",
      expertise: "Shipbuilding, Complex Project Delivery, Technical Management",
      gradient: "from-blue-500 to-cyan-500",
      icon: <FaShip className="w-5 h-5" />
    },
    {
      initials: "CMK",
      name: "Ch. Mahesh Kumar",
      role: "Project Coordinator",
      description: "DME with 5+ years at MGRC; specialized in procurement and technical oversight of project execution.",
      expertise: "Procurement, Technical Oversight, Project Coordination",
      gradient: "from-amber-500 to-orange-500",
      icon: <HiOutlineCog className="w-5 h-5" />
    },
    {
      initials: "BDR",
      name: "B. Dharma Raju",
      role: "Manager (Quality Control)",
      description: "DME; Retd. Deputy Manager (Drydocks & Slipway) ND(V); 35 years in shipbuilding/repairs; QC expert.",
      expertise: "Quality Control, Shipbuilding, NDT Inspection",
      gradient: "from-purple-500 to-pink-500",
      icon: <FaCheckCircle className="w-5 h-5" />
    },
    {
      initials: "GB",
      name: "George Benjamin",
      role: "Site Engineer",
      description: "Retd. HSL with 32 years in structural works during ship construction and repairs.",
      expertise: "Structural Works, Ship Construction, Site Management",
      gradient: "from-green-500 to-emerald-500",
      icon: <FaToolbox className="w-5 h-5" />
    },
    {
      initials: "BSK",
      name: "B Sharath Kumar",
      role: "Engineer (Quality Control)",
      description: "DME; ASNT Level-II (RT/UT/PT/MPT); experience with L&T ECC and SBC(V).",
      expertise: "NDT Testing, Quality Assurance, ASNT Level-II Certification",
      gradient: "from-red-500 to-pink-500",
      icon: <FaCertificate className="w-5 h-5" />
    }
  ];

  const timeline = [
    {
      year: "1984",
      title: "Company Foundation",
      description: "M G Rajeev & Co established by late Shri M. G. Rajeev with a vision to provide quality engineering and fabrication services to industrial clients in Visakhapatnam.",
      gradient: "from-amber-500 to-orange-500",
      icon: <FaBuilding className="w-6 h-6" />
    },
    {
      year: "1990",
      title: "First Major Defence Project",
      description: "Successfully completed first major project for Indian Navy, establishing our reputation in defence sector and marine infrastructure works.",
      gradient: "from-blue-500 to-cyan-500",
      icon: <FaShieldAlt className="w-6 h-6" />
    },
    {
      year: "2000",
      title: "ISO 9001:2008 Certification",
      description: "Achieved ISO 9001:2008 certification for Quality Management System, demonstrating our commitment to international quality standards.",
      gradient: "from-purple-500 to-pink-500",
      icon: <FaAward className="w-6 h-6" />
    },
    {
      year: "2010",
      title: "Hydro Power Sector Entry",
      description: "Expanded into hydro-mechanical works with major projects for Tehri Dam, establishing expertise in penstocks, gates, and hydro-mechanical systems.",
      gradient: "from-green-500 to-emerald-500",
      icon: <FaWater className="w-6 h-6" />
    },
    {
      year: "2020",
      title: "Digital Transformation",
      description: "Implemented digital QA/QC systems, modern construction methods, and advanced project management tools for enhanced efficiency and quality.",
      gradient: "from-red-500 to-pink-500",
      icon: <HiOutlineLightningBolt className="w-6 h-6" />
    },
    {
      year: "2025",
      title: "Future Growth",
      description: "Strategic roadmap for 2025-2030 focusing on defence & marine EPC expansion, hydro-mechanical excellence, and digital delivery capabilities.",
      gradient: "from-indigo-500 to-purple-500",
      icon: <FaRocket className="w-6 h-6" />
    }
  ];

  const certifications = [
    {
      icon: <FaCertificate className="w-8 h-8" />,
      title: "ISO 9001:2008",
      description: "Quality Management System",
      gradient: "from-amber-500 to-orange-500"
    },
    {
      icon: <FaShieldAlt className="w-8 h-8" />,
      title: "DGNP(V)",
      description: "Defence Registered Contractor",
      gradient: "from-blue-500 to-cyan-500"
    },
    {
      icon: <FaShip className="w-8 h-8" />,
      title: "Naval Dockyard",
      description: "Approved Marine Contractor",
      gradient: "from-purple-500 to-pink-500"
    },
    {
      icon: <FaCogs className="w-8 h-8" />,
      title: "BIS/IS/ASME",
      description: "Standards Compliance",
      gradient: "from-green-500 to-emerald-500"
    }
  ];

  return (
    <>
      {/* SEO Meta Tags for About Us Page */}
  <head>
    <title>About M G Rajeev & Co | 40+ Years Engineering Excellence | Leadership Team</title>
    <meta 
      name="description" 
      content="Learn about M G Rajeev & Co's 40+ year legacy in engineering excellence. Meet our leadership team, explore our vision, mission, and journey since 1984." 
    />
    <meta 
      name="keywords" 
      content="about M G Rajeev & Co, engineering company history, leadership team, company vision, 40 years experience, EPC contractor about, technical team, company timeline" 
    />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="index, follow" />
    
    {/* Open Graph Meta Tags */}
    <meta property="og:title" content="About M G Rajeev & Co | 40+ Years Engineering Excellence" />
    <meta 
      property="og:description" 
      content="Discover our legacy of engineering excellence since 1984. Meet our experienced leadership and technical teams driving quality and innovation." 
    />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.mgrajeevco.com/about" />
    <meta property="og:image" content="https://www.mgrajeevco.com/images/about-company-legacy.jpg" />
    
    {/* Twitter Card Meta Tags */}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="About M G Rajeev & Co | Engineering Legacy Since 1984" />
    <meta 
      name="twitter:description" 
      content="40+ years of engineering excellence in defence, marine, and hydro-mechanical infrastructure. Meet our expert team and explore our journey." 
    />
    <meta name="twitter:image" content="https://www.mgrajeevco.com/images/about-twitter-card.jpg" />
    
    {/* Canonical URL */}
    <link rel="canonical" href="https://www.mgrajeevco.com/about" />
    
    {/* Structured Data for About Page */}
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{
        __html: JSON.stringify({
          "@context": "https://schema.org",
          "@type": "AboutPage",
          "name": "About M G Rajeev & Co",
          "description": "Learn about our 40+ year legacy in engineering excellence, leadership team, and company history",
          "publisher": {
            "@type": "Organization",
            "name": "M G Rajeev & Co",
            "description": "Premier engineering and EPC contractor specializing in hydro-mechanical works, heavy steel structures, and marine infrastructure",
            "foundingDate": "1984",
            "address": {
              "@type": "PostalAddress",
              "addressLocality": "Visakhapatnam",
              "addressRegion": "Andhra Pradesh",
              "addressCountry": "India"
            },
            "numberOfEmployees": {
              "@type": "QuantitativeValue",
              "value": "50+"
            },
            "founder": {
              "@type": "Person",
              "name": "Shri M. G. Rajeev"
            }
          },
          "mainEntity": {
            "@type": "Organization",
            "name": "M G Rajeev & Co",
            "foundingDate": "1984",
            "founder": {
              "@type": "Person",
              "name": "Shri M. G. Rajeev"
            },
            "description": "Engineering and EPC contractor with 40+ years of excellence in defence, marine, and hydro-mechanical infrastructure",
            "numberOfEmployees": "50+",
            "areaServed": "India",
            "knowsAbout": [
              "Engineering",
              "EPC Contractor",
              "Steel Fabrication",
              "Hydro Power Infrastructure",
              "Marine Construction",
              "Defence Infrastructure"
            ]
          }
        })
      }}
    />
  </head>
      {/* Hero Section */}
      <section className="pt-24 pb-16 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-white/10"></div>
        <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-blue-500"></div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center text-white">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              About <span className="text-amber-400">M G Rajeev & Co</span>
            </h1>
            <p className="text-xl md:text-2xl text-gray-300 max-w-4xl mx-auto leading-relaxed">
              Four decades of engineering excellence, built on reliability,
              driven by quality, and sustained by trust in every project we
              undertake.
            </p>
          </div>
        </div>
      </section>

      {/* Company Background */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            {/* Company Story */}
            <div>
              <div className="inline-flex items-center gap-3 mb-4">
                <div className="w-12 h-0.5 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
                <span className="text-blue-600 font-semibold uppercase tracking-wider text-sm">Our Story</span>
                <div className="w-12 h-0.5 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
              </div>
              <h2 className="text-4xl font-bold text-slate-900 mb-6">
                Our <span className="text-blue-600">Legacy</span>
              </h2>
              <p className="text-lg text-slate-600 mb-6 leading-relaxed">
                Founded in 1984 by the late Shri M. G. Rajeev, our company has
                grown from a modest fabrication workshop to become one of
                Visakhapatnam's most respected engineering and EPC contractors.
                What started as a vision to deliver quality engineering
                solutions has evolved into a legacy of excellence spanning four
                decades.
              </p>
              <p className="text-lg text-slate-600 mb-6 leading-relaxed">
                Today, under the leadership of S. Rukmani as Managing Partner,
                we continue to uphold the founding principles of reliability,
                quality craftsmanship, and unwavering commitment to client
                satisfaction. Our journey has been marked by continuous
                innovation, technical excellence, and successful project
                delivery across diverse sectors.
              </p>
              <p className="text-lg text-slate-600 mb-8 leading-relaxed">
                We have established ourselves as a trusted partner for premier
                clients including the Indian Navy, DGNP, Naval Dockyard, and
                leading industrial houses, delivering complex, time-critical
                projects with millimetre precision and uncompromising quality.
              </p>
              <div className="grid grid-cols-2 gap-6">
                <div className="text-center p-6 bg-slate-50 rounded-xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                  <div className="text-3xl font-bold text-blue-600">
                    {yearsCount}+
                  </div>
                  <div className="text-sm text-slate-600 font-medium">
                    Years of Excellence
                  </div>
                </div>
                <div className="text-center p-6 bg-slate-50 rounded-xl border border-slate-200 hover:shadow-lg transition-all duration-300">
                  <div className="text-3xl font-bold text-amber-600">
                    {projectsCount}+
                  </div>
                  <div className="text-sm text-slate-600 font-medium">
                    Projects Completed
                  </div>
                </div>
              </div>
            </div>
            
            {/* Company Image */}
            <div className="relative">
              <div className="rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
                <div className="w-full h-96 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center">
                  <div className="text-white text-center">
                    <FaIndustry className="w-16 h-16 mx-auto mb-4" />
                    <p className="text-lg font-semibold">Engineering Excellence Since 1984</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Vision, Mission & Values */}
      <section className="py-20 bg-slate-50 relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Vision, Mission & <span className="text-amber-600">Core Values</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Our guiding principles that drive every decision and shape our
              commitment to engineering excellence and client satisfaction.
            </p>
          </div>
          
          <div className="grid lg:grid-cols-3 gap-8">
            {/* Vision */}
            <div className="group bg-white border border-slate-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                <FaEye className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-4">
                Vision
              </h3>
              <p className="text-slate-600 leading-relaxed">
                To be a trusted EPC partner of choice for defence, marine and
                hydro-mechanical infrastructure—delivering reliable, safe and
                sustainable engineering outcomes that exceed client expectations
                and contribute to national development.
              </p>
            </div>
            
            {/* Mission */}
            <div className="group bg-white border border-slate-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-amber-500 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                <FaBullseye className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-4">
                Mission
              </h3>
              <ul className="text-slate-600 space-y-3">
                {[
                  "Deliver projects on time with uncompromising quality and safety standards",
                  "Invest in people, processes and equipment to scale responsibly",
                  "Adopt modern construction methods and digital QA/QC systems",
                  "Build long-term partnerships with defence and public-sector clients"
                ].map((item, index) => (
                  <li key={index} className="flex items-start gap-3">
                    <FaCheckCircle className="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" />
                    <span>{item}</span>
                  </li>
                ))}
              </ul>
            </div>
            
            {/* Core Values */}
            <div className="group bg-white border border-slate-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-purple-500 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                <FaHeart className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-2xl font-bold text-slate-900 mb-4">
                Core Values
              </h3>
              <ul className="text-slate-600 space-y-3">
                {[
                  "Reliability and accountability in all commitments",
                  "Quality and craftsmanship in every detail",
                  "Safety first approach to all operations",
                  "Integrity and compliance with all standards",
                  "Continuous improvement in processes and performance"
                ].map((item, index) => (
                  <li key={index} className="flex items-start gap-3">
                    <FaCheckCircle className="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" />
                    <span>{item}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Leadership Team */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Leadership & <span className="text-blue-600">Core Team</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Experienced professionals and technical experts leading our
              commitment to engineering excellence and client satisfaction.
            </p>
          </div>
          
          {/* Leadership */}
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            {leadership.map((person, index) => (
              <div key={index} className="group text-center bg-white border border-slate-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div className={`inline-flex items-center justify-center w-32 h-32 bg-gradient-to-r ${person.gradient} rounded-full mb-6 group-hover:scale-110 transition-transform duration-300`}>
                  <div className="text-white text-2xl font-bold">{person.initials}</div>
                </div>
                <h3 className="text-xl font-bold text-slate-900 mb-2">
                  {person.name}
                </h3>
                <p className="text-orange-600 font-semibold mb-3">
                  {person.role}
                </p>
                <p className="text-slate-600 text-sm leading-relaxed">
                  {person.description}
                </p>
              </div>
            ))}
          </div>
          
          {/* Technical Team */}
          <div className="bg-slate-50 border border-slate-200 rounded-2xl p-8 shadow-lg">
            <h3 className="text-2xl font-bold text-center text-slate-900 mb-8">
              Key Technical & Project Management Team
            </h3>
            <div className="grid md:grid-cols-2 gap-8">
              {technicalTeam.map((person, index) => (
                <div key={index} className="group bg-white border border-slate-200 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                  <div className="flex items-start space-x-4">
                    <div className={`inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r ${person.gradient} rounded-2xl text-white group-hover:scale-110 transition-transform duration-300`}>
                      {person.icon}
                    </div>
                    <div className="flex-1">
                      <h4 className="text-lg font-bold text-slate-900">
                        {person.name}
                      </h4>
                      <p className="text-orange-600 font-semibold text-sm mb-2">
                        {person.role}
                      </p>
                      <p className="text-slate-600 text-sm mb-2 leading-relaxed">
                        {person.description}
                      </p>
                      <div className="text-xs text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                        <strong>Expertise:</strong> {person.expertise}
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Company Timeline */}
      <section className="py-20 bg-slate-50 relative overflow-hidden">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Our <span className="text-amber-600">Journey</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Four decades of milestones and achievements that have shaped our
              reputation for engineering excellence and client trust.
            </p>
          </div>
          
          <div className="space-y-8">
            {timeline.map((item, index) => (
              <div key={index} className="group relative">
                <div className="flex items-start gap-6">
                  <div className={`flex-shrink-0 w-20 h-20 bg-gradient-to-r ${item.gradient} rounded-2xl flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300`}>
                    {item.icon}
                  </div>
                  <div className="flex-1 bg-white border border-slate-200 p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div className="flex items-center gap-3 mb-2">
                      <div className="text-2xl font-bold text-slate-900">
                        {item.year}
                      </div>
                    </div>
                    <h3 className="text-xl font-bold text-slate-900 mb-3">
                      {item.title}
                    </h3>
                    <p className="text-slate-600 leading-relaxed">
                      {item.description}
                    </p>
                  </div>
                </div>
                {index < timeline.length - 1 && (
                  <div className="absolute left-10 top-20 w-0.5 h-8 bg-slate-300"></div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Certifications */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Certifications & <span className="text-blue-600">Compliance</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Our commitment to quality, safety, and regulatory compliance is
              validated through recognized certifications and adherence to
              industry standards.
            </p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {certifications.map((cert, index) => (
              <div key={index} className="group text-center bg-white border border-slate-200 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div className={`inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r ${cert.gradient} rounded-2xl mb-6 mx-auto group-hover:scale-110 transition-transform duration-300`}>
                  {cert.icon}
                </div>
                <h3 className="text-xl font-bold text-slate-900 mb-2">
                  {cert.title}
                </h3>
                <p className="text-slate-600 text-sm">
                  {cert.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Call to Action */}
      <section className="py-20 bg-slate-900 relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
            Partner With Experience
          </h2>
          <p className="text-xl text-slate-300 mb-12 max-w-3xl mx-auto leading-relaxed">
            With 40+ years of proven expertise and a commitment to excellence,
            we're ready to bring your next engineering project to life.
          </p>
          <div className="flex flex-col sm:flex-row gap-6 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              Start Your Project
              <FaRocket className="w-5 h-5" />
            </Link>
            <Link
              href="/capabilities"
              className="inline-flex items-center gap-3 bg-transparent border-2 border-white text-white hover:bg-white hover:text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300"
            >
              Our Services
              <FaCogs className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}

export default AboutUs;