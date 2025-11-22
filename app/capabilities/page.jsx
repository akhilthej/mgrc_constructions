"use client";
import React, { useState } from "react";
import Link from "next/link";
import {
  FaWater,
  FaIndustry,
  FaShip,
  FaSprayCan,
  FaHardHat,
  FaCogs,
  FaCertificate,
  FaShieldAlt,
  FaExclamationTriangle,
  FaUserCheck,
  FaWrench,
  FaCut,
  FaWeightHanging,
  FaRuler,
  FaCheckCircle,
  FaBolt,
  FaCog,
  FaIndustry as FaIndustryIcon
} from "react-icons/fa";

function CapabilitiesPage() {
  const [activeEquipmentFilter, setActiveEquipmentFilter] = useState("all");

  const services = [
    {
      icon: <FaWater className="w-8 h-8" />,
      title: "Hydro-mechanical Works",
      description: "Complete fabrication, erection, testing & commissioning of hydro-mechanical equipment including penstocks, radial/vertical gates, gantries, and hoists for hydro power projects.",
      expertise: 95,
      features: [
        "Penstocks and pressure shafts",
        "Radial and vertical gates",
        "Gantry cranes and hoists",
        "Stop log gates and trash racks",
        "Hydro-mechanical equipment"
      ],
      gradient: "from-blue-500 to-cyan-500",
      buttonColor: "from-blue-600 to-cyan-600"
    },
    {
      icon: <FaIndustry className="w-8 h-8" />,
      title: "Heavy Structural Fabrication",
      description: "Heavy structural steel fabrication and installation for industrial, marine, and infrastructure projects with precision engineering and quality craftsmanship.",
      expertise: 92,
      features: [
        "Industrial steel structures",
        "Marine and offshore structures",
        "Bridge and infrastructure components",
        "Pressure vessels and tanks",
        "Custom fabrication solutions"
      ],
      gradient: "from-amber-500 to-orange-500",
      buttonColor: "from-amber-600 to-orange-600"
    },
    {
      icon: <FaShip className="w-8 h-8" />,
      title: "Marine & Dockyard Works",
      description: "Specialized marine construction including caisson gates, dock blocks, slipway upgrades, and naval infrastructure for defence sectors with precision engineering.",
      expertise: 90,
      features: [
        "Caisson gates and dock blocks",
        "Slipway upgrades and modifications",
        "Naval dockyard infrastructure",
        "Marine structural works",
        "Ship repair facilities"
      ],
      gradient: "from-purple-500 to-pink-500",
      buttonColor: "from-purple-600 to-pink-600"
    },
    {
      icon: <FaSprayCan className="w-8 h-8" />,
      title: "Surface Preparation & Coatings",
      description: "Professional surface preparation and protective coatings using advanced blasting and painting techniques for long-lasting protection in harsh environments.",
      expertise: 88,
      features: [
        "Hydro and abrasive blasting",
        "Airless spray painting",
        "Protective coating systems",
        "Surface treatment solutions",
        "Corrosion protection"
      ],
      gradient: "from-green-500 to-emerald-500",
      buttonColor: "from-green-600 to-emerald-600"
    },
    {
      icon: <FaHardHat className="w-8 h-8" />,
      title: "Civil Engineering Works",
      description: "Comprehensive civil engineering services including repairs, upgradation, roofing, cladding, roads, and drainage systems for industrial and infrastructure projects.",
      expertise: 85,
      features: [
        "Building repairs and upgradation",
        "Roofing and cladding systems",
        "Road and drainage construction",
        "Infrastructure development",
        "Structural repairs"
      ],
      gradient: "from-indigo-500 to-purple-500",
      buttonColor: "from-indigo-600 to-purple-600"
    },
    {
      icon: <FaCogs className="w-8 h-8" />,
      title: "Electrical & Mechanical Works",
      description: "Complete electrical and mechanical solutions including motors, pumps, cranes, and control panels for industrial applications with reliable performance.",
      expertise: 87,
      features: [
        "Motor and pump installations",
        "Crane and hoist systems",
        "Control panel fabrication",
        "Electrical system upgrades",
        "Automation systems"
      ],
      gradient: "from-red-500 to-pink-500",
      buttonColor: "from-red-600 to-pink-600"
    }
  ];

  const equipment = [
    {
      category: "welding",
      title: "Welding Equipment",
      image: "https://kimi-web-img.moonshot.cn/img/cdn.thefabricator.com/a4b2998e2d477dc7b936d4b24410686da5061323.JPG",
      details: {
        inventory: "60+ units combined",
        types: "Transformers, Rectifiers, Inverters, Generators",
        specialized: "7 MIG welding machines"
      },
      features: [
        "Welding transformers/rectifiers/inverters/generators",
        "MIG welding machines (7 units)",
        "Electrode ovens (9 stationary + portable quivers)"
      ],
      icon: <FaWrench className="w-6 h-6" />
    },
    {
      category: "cutting",
      title: "Cutting & Drilling Tools",
      image: "https://kimi-web-img.moonshot.cn/img/www.cnsteelstructures.com/49499943a7b5e8d9f81db350339668ee9f4d5e44.jpg",
      details: {
        gasCutting: "25 sets",
        pugCutting: "18 machines",
        drilling: "9 machines + 7 magnetic-base drills"
      },
      features: [
        "Gas cutting sets (25)",
        "Pug cutting machines (18)",
        "Drilling machines (9) and magnetic-base drills (7)"
      ],
      icon: <FaCut className="w-6 h-6" />
    },
    {
      category: "welding",
      title: "Grinding & Finishing",
      image: "https://kimi-web-img.moonshot.cn/img/my.synergia-eng.com/7d2b02485a9f40c3f22dc1d6eb6f617baf51d79a.jpg",
      details: {
        grinders: "50+ units (AG9, AG7, GQ4)",
        flexibleGrinders: "FF2 models",
        applications: "Surface finishing, weld preparation"
      },
      features: [
        "Grinders (AG9, AG7, GQ4) - 50+ units",
        "FF2/flexible grinders",
        "Surface preparation tools"
      ],
      icon: <FaCog className="w-6 h-6" />
    },
    {
      category: "lifting",
      title: "Lifting & Handling",
      image: "https://kimi-web-img.moonshot.cn/img/www.c-nct.com/d7ace124fb125c394743b80bdda7e5e66b90380c.jpg",
      details: {
        hydraCranes: "12-14T (2 units)",
        jacks: "5-40T capacity",
        chainPulley: "1-10T capacity"
      },
      features: [
        "Hydra cranes 12-14T (2 nos.)",
        "Jacks (5-40T), Chain pulley blocks (1-10T)",
        "Power/manual winches and rigging"
      ],
      icon: <FaWeightHanging className="w-6 h-6" />
    },
    {
      category: "testing",
      title: "Surface Preparation",
      image: "https://kimi-web-img.moonshot.cn/img/theshawgrp.com/e3b475195e57ae1fbff7b64f433b45657b748108.jpg",
      details: {
        blasting: "3 units",
        compressors: "4 units",
        hydroBlasting: "2 UHP machines"
      },
      features: [
        "Sand/shot blasting equipment (3)",
        "Air compressors (4)",
        "Hydro blasting (UHP) machines (2)"
      ],
      icon: <FaSprayCan className="w-6 h-6" />
    },
    {
      category: "testing",
      title: "Testing & Quality Control",
      image: "https://kimi-web-img.moonshot.cn/img/veritassteel.com/4080d27e11c4ebe09ac275c11475681a26f39a80.jpg",
      details: {
        testPumps: "7 units",
        jetCleaners: "2 units",
        ndt: "ASNT Level-II certified"
      },
      features: [
        "Hydraulic test pumps (7)",
        "HP jet cleaners (2)",
        "NDT equipment and calibrated tools"
      ],
      icon: <FaRuler className="w-6 h-6" />
    }
  ];

  const qualityStandards = [
    {
      icon: <FaCertificate className="w-8 h-8" />,
      title: "ISO 9001:2008 Compliant QMS",
      description: "Documented Inspection Test Plans (ITPs), Welding Procedure Specifications (WPS/PQRs), and calibrated tools ensuring consistent quality delivery.",
      color: "from-blue-500 to-cyan-500"
    },
    {
      icon: <FaShieldAlt className="w-8 h-8" />,
      title: "Standards Compliance",
      description: "Strict adherence to MES/Indian Navy specifications and relevant BIS/IS/ASME standards for all fabrication and construction activities.",
      color: "from-amber-500 to-orange-500"
    },
    {
      icon: <FaCheckCircle className="w-8 h-8" />,
      title: "Digital QA/QC Systems",
      description: "Digital quality assurance logs for material receipt, inspection, test results, and NCR closure with real-time tracking and documentation.",
      color: "from-purple-500 to-pink-500"
    }
  ];

  const safetyStandards = [
    {
      icon: <FaExclamationTriangle className="w-8 h-8" />,
      title: "Zero-Harm Policy",
      description: "Comprehensive HSE policy emphasizing zero-harm approach with mandatory PPE, regular toolbox talks, and detailed method statements for all operations.",
      color: "from-red-500 to-pink-500"
    },
    {
      icon: <FaShieldAlt className="w-8 h-8" />,
      title: "Risk Assessment",
      description: "Systematic risk assessment and mitigation procedures with regular safety audits, emergency response plans, and continuous safety training programs.",
      color: "from-orange-500 to-amber-500"
    },
    {
      icon: <FaUserCheck className="w-8 h-8" />,
      title: "Compliance & Training",
      description: "Regular safety training, compliance monitoring, and certification programs ensuring all personnel maintain highest safety standards and regulatory compliance.",
      color: "from-green-500 to-emerald-500"
    }
  ];

  const filteredEquipment = activeEquipmentFilter === "all" 
    ? equipment 
    : equipment.filter(item => item.category === activeEquipmentFilter);

  return (
    <>
          {/* SEO Meta Tags */}
      <head>
        <title>Engineering Capabilities | M G Rajeev & Co | Hydro-mechanical & Structural Works</title>
        <meta 
          name="description" 
          content="Explore M G Rajeev & Co's comprehensive engineering capabilities: Hydro-mechanical works, heavy structural fabrication, marine infrastructure, surface preparation, and advanced equipment." 
        />
        <meta 
          name="keywords" 
          content="engineering capabilities, hydro-mechanical works, structural fabrication, marine infrastructure, surface preparation, EPC contractor, welding equipment, quality standards, safety protocols" 
        />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="index, follow" />
        
        {/* Open Graph Meta Tags */}
        <meta property="og:title" content="Engineering Capabilities | M G Rajeev & Co" />
        <meta 
          property="og:description" 
          content="Comprehensive engineering services: Hydro-mechanical works, heavy structural fabrication, marine infrastructure with 40+ years expertise and advanced equipment." 
        />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://www.mgrajeevco.com/capabilities" />
        <meta property="og:image" content="https://www.mgrajeevco.com/images/capabilities-engineering-services.jpg" />
        
        {/* Twitter Card Meta Tags */}
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Engineering Capabilities | M G Rajeev & Co" />
        <meta 
          name="twitter:description" 
          content="40+ years expertise in hydro-mechanical works, structural fabrication, marine infrastructure with advanced equipment and quality standards." 
        />
        <meta name="twitter:image" content="https://www.mgrajeevco.com/images/capabilities-twitter-card.jpg" />
        
        {/* Canonical URL */}
        <link rel="canonical" href="https://www.mgrajeevco.com/capabilities" />
        
        {/* Structured Data for Capabilities Page */}
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "Service",
              "name": "Engineering Capabilities - M G Rajeev & Co",
              "description": "Comprehensive engineering and construction services including hydro-mechanical works, heavy structural fabrication, marine infrastructure, and surface preparation",
              "provider": {
                "@type": "Organization",
                "name": "M G Rajeev & Co",
                "description": "Premier engineering and EPC contractor with 40+ years of excellence"
              },
              "areaServed": "India",
              "serviceType": [
                "Hydro-mechanical Works",
                "Heavy Structural Fabrication", 
                "Marine & Dockyard Works",
                "Surface Preparation & Coatings",
                "Civil Engineering Works",
                "Electrical & Mechanical Works"
              ],
              "hasOfferCatalog": {
                "@type": "OfferCatalog",
                "name": "Engineering Services",
                "itemListElement": [
                  {
                    "@type": "Offer",
                    "itemOffered": {
                      "@type": "Service",
                      "name": "Hydro-mechanical Works",
                      "description": "Fabrication, erection, testing & commissioning of penstocks, radial/vertical gates, gantries, and hoists"
                    }
                  },
                  {
                    "@type": "Offer",
                    "itemOffered": {
                      "@type": "Service",
                      "name": "Heavy Structural Fabrication",
                      "description": "Heavy structural steel fabrication and installation for industrial, marine, and infrastructure projects"
                    }
                  },
                  {
                    "@type": "Offer",
                    "itemOffered": {
                      "@type": "Service",
                      "name": "Marine & Dockyard Works",
                      "description": "Specialized marine construction including caisson gates, dock blocks, and naval infrastructure"
                    }
                  }
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
              Our <span className="text-amber-400">Capabilities</span>
            </h1>
            <p className="text-xl md:text-2xl text-gray-300 max-w-4xl mx-auto leading-relaxed">
              Comprehensive engineering and construction services backed by 40+
              years of expertise, advanced technology, and unwavering commitment
              to quality and safety.
            </p>
          </div>
        </div>
      </section>

      {/* Core Services Overview */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Core <span className="text-blue-600">Services</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              From concept to completion, we deliver comprehensive engineering
              solutions across diverse sectors with precision, quality, and
              reliability.
            </p>
          </div>

          {/* Services Grid */}
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {services.map((service, index) => (
              <div key={index} className="group bg-white border border-slate-200 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div className={`inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r ${service.gradient} rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300`}>
                  {service.icon}
                </div>
                
                <h3 className="text-2xl font-bold text-slate-900 mb-4">
                  {service.title}
                </h3>
                
                <p className="text-slate-600 mb-6 leading-relaxed">
                  {service.description}
                </p>

                {/* Expertise Level */}
                <div className="mb-6">
                  <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-semibold text-slate-700">
                      Expertise Level
                    </span>
                    <span className="text-sm font-bold text-amber-600">
                      {service.expertise}%
                    </span>
                  </div>
                  <div className="bg-slate-200 rounded-full h-3">
                    <div 
                      className={`h-3 rounded-full bg-gradient-to-r ${service.gradient} transition-all duration-1000`}
                      style={{ width: `${service.expertise}%` }}
                    />
                  </div>
                </div>

                <ul className="space-y-3 mb-6">
                  {service.features.map((feature, featureIndex) => (
                    <li key={featureIndex} className="flex items-center gap-3 text-slate-700 text-sm">
                      <FaCheckCircle className={`w-4 h-4 ${service.gradient.replace('from-', 'text-').split(' ')[0]}`} />
                      {feature}
                    </li>
                  ))}
                </ul>

                <div className="text-center">
                  <button className={`bg-gradient-to-r ${service.buttonColor} hover:shadow-lg text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 transform`}>
                    Learn More
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Equipment & Machinery */}
      <section className="py-20 bg-slate-50 relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Plant & <span className="text-amber-600">Machinery</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              State-of-the-art equipment and machinery enabling us to deliver
              complex projects with precision, efficiency, and quality.
            </p>
          </div>

          {/* Equipment Categories */}
          <div className="flex flex-wrap justify-center gap-4 mb-12">
            {["all", "welding", "cutting", "testing", "lifting"].map((filter) => (
              <button
                key={filter}
                onClick={() => setActiveEquipmentFilter(filter)}
                className={`px-6 py-3 rounded-xl font-semibold transition-all duration-300 ${
                  activeEquipmentFilter === filter
                    ? "bg-gradient-to-r from-amber-500 to-red-500 text-white shadow-lg"
                    : "bg-white text-slate-700 hover:bg-slate-100 hover:shadow-lg border border-slate-200"
                }`}
              >
                {filter === "all" ? "All Equipment" : filter.charAt(0).toUpperCase() + filter.slice(1)}
              </button>
            ))}
          </div>

          {/* Equipment Grid */}
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {filteredEquipment.map((item, index) => (
              <div key={index} className="group bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div className="relative h-48 overflow-hidden">
                  {/* Using regular img tag instead of Next.js Image */}
                  <img
                    src={item.image}
                    alt={item.title}
                    className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" />
                  <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-lg p-2">
                    {item.icon}
                  </div>
                </div>
                
                <div className="p-6">
                  <h3 className="text-xl font-bold text-slate-900 mb-4">
                    {item.title}
                  </h3>
                  
                  <div className="text-slate-600 mb-4 space-y-2">
                    {Object.entries(item.details).map(([key, value]) => (
                      <p key={key} className="text-sm">
                        <strong>{key.charAt(0).toUpperCase() + key.slice(1).replace(/([A-Z])/g, ' $1')}:</strong> {value}
                      </p>
                    ))}
                  </div>
                  
                  <div className="text-sm text-slate-700 space-y-2">
                    {item.features.map((feature, featureIndex) => (
                      <div key={featureIndex} className="flex items-center gap-2">
                        <FaCheckCircle className="w-3 h-3 text-amber-500 flex-shrink-0" />
                        <span className="text-sm">{feature}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Quality & Safety Standards */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Quality & <span className="text-blue-600">Safety Standards</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Our commitment to excellence is demonstrated through rigorous
              quality control processes and comprehensive safety protocols that
              exceed industry standards.
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-12">
            {/* Quality Management */}
            <div>
              <h3 className="text-3xl font-bold text-slate-900 mb-8 text-center">
                Quality Management
              </h3>
              <div className="space-y-6">
                {qualityStandards.map((standard, index) => (
                  <div key={index} className="group bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div className="flex items-start space-x-4">
                      <div className={`bg-gradient-to-r ${standard.color} rounded-2xl p-4 text-white group-hover:scale-110 transition-transform duration-300`}>
                        {standard.icon}
                      </div>
                      <div className="flex-1">
                        <h4 className="font-semibold text-slate-900 mb-2 text-lg">
                          {standard.title}
                        </h4>
                        <p className="text-slate-600 leading-relaxed">
                          {standard.description}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Safety Management */}
            <div>
              <h3 className="text-3xl font-bold text-slate-900 mb-8 text-center">
                Safety Management
              </h3>
              <div className="space-y-6">
                {safetyStandards.map((standard, index) => (
                  <div key={index} className="group bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div className="flex items-start space-x-4">
                      <div className={`bg-gradient-to-r ${standard.color} rounded-2xl p-4 text-white group-hover:scale-110 transition-transform duration-300`}>
                        {standard.icon}
                      </div>
                      <div className="flex-1">
                        <h4 className="font-semibold text-slate-900 mb-2 text-lg">
                          {standard.title}
                        </h4>
                        <p className="text-slate-600 leading-relaxed">
                          {standard.description}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Call to Action */}
      <section className="py-20 bg-slate-900 relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
            Ready to Start Your Project?
          </h2>
          <p className="text-xl text-slate-300 mb-12 max-w-3xl mx-auto leading-relaxed">
            With comprehensive capabilities, advanced equipment, and 40+ years
            of expertise, we're equipped to handle your most challenging
            engineering requirements.
          </p>
          <div className="flex flex-col sm:flex-row gap-6 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              Get Quote
              <FaBolt className="w-5 h-5" />
            </Link>
            <Link
              href="/projects"
              className="inline-flex items-center gap-3 bg-transparent border-2 border-white text-white hover:bg-white hover:text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300"
            >
              View Our Work
              <FaIndustryIcon className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}

export default CapabilitiesPage;