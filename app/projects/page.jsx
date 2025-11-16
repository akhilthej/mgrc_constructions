"use client";
import React, { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import {
  FaCheckCircle,
  FaClock,
  FaMapMarkerAlt,
  FaUserTie,
  FaMoneyBillWave,
  FaCalendarAlt,
  FaIndustry,
  FaWater,
  FaShieldAlt,
  FaHardHat,
  FaTools,
  FaChartLine,
  FaAward,
  FaRocket,
  FaTimes
} from "react-icons/fa";
import {
  HiOutlineClipboardCheck,
  HiOutlineTrendingUp
} from "react-icons/hi";

// Project data - you can move this to a separate file
const projectsData = {
  completed: [
    {
      id: "tehri-dam",
      title: "Tehri Dam Hydro-mechanical Works",
      category: "hydro",
      location: "Uttarakhand",
      client: "THDC Ltd",
      duration: "24 months",
      description: "Complete hydro-mechanical works including penstocks, radial gates, gantry cranes, and hoists for one of India's largest hydro power projects. Project involved fabrication, erection, testing and commissioning of critical water management systems.",
      image: "https://kimi-web-img.moonshot.cn/img/www.cnsteelstructures.com/49499943a7b5e8d9f81db350339668ee9f4d5e44.jpg",
      icon: <FaWater className="w-6 h-6" />
    },
    {
      id: "naval-dockyard",
      title: "Naval Dockyard Marine Infrastructure",
      category: "defence",
      location: "Visakhapatnam",
      client: "Indian Navy",
      duration: "18 months",
      description: "Construction of caisson gates, dock blocks, slipway upgrades, and marine structures for Indian Navy's strategic naval base. Project included specialized marine fabrication and precision installation works.",
      image: "https://kimi-web-img.moonshot.cn/img/my.synergia-eng.com/7d2b02485a9f40c3f22dc1d6eb6f617baf51d79a.jpg",
      icon: <FaShieldAlt className="w-6 h-6" />
    },
    {
      id: "steel-plant",
      title: "Integrated Mini Steel Plant",
      category: "industrial",
      location: "Kothavalasa",
      client: "Maa Maha Maya Industries",
      duration: "30 months",
      description: "Complete fabrication, erection, testing and commissioning of integrated steel plant including heavy structural works, process equipment, and industrial infrastructure for steel manufacturing facility.",
      image: "https://kimi-web-img.moonshot.cn/img/cdn.thefabricator.com/a4b2998e2d477dc7b936d4b24410686da5061323.JPG",
      icon: <FaIndustry className="w-6 h-6" />
    },
    {
      id: "indravati-dam",
      title: "Upper Indravati Dam Works",
      category: "hydro",
      location: "Odisha",
      client: "Odisha Construction Corp",
      duration: "20 months",
      description: "Hydro-mechanical works including radial gates, vertical gates, and associated infrastructure for water management system. Project completed with precision engineering and quality craftsmanship.",
      image: "https://kimi-web-img.moonshot.cn/img/www.cnsteelstructures.com/49499943a7b5e8d9f81db350339668ee9f4d5e44.jpg",
      icon: <FaWater className="w-6 h-6" />
    },
    {
      id: "floating-canopy",
      title: "Floating Canopy for Indian Navy",
      category: "defence",
      location: "Visakhapatnam",
      client: "Ship Building Centre (V)",
      duration: "15 months",
      description: "Design, manufacture, installation & commissioning of Floating Canopy - first-of-its-kind development for Indian Navy at Ship Building Centre. Innovative marine engineering solution with modular design approach.",
      image: "https://kimi-web-img.moonshot.cn/img/my.synergia-eng.com/7d2b02485a9f40c3f22dc1d6eb6f617baf51d79a.jpg",
      icon: <FaShieldAlt className="w-6 h-6" />
    },
    {
      id: "gondi-dam",
      title: "Gondi Dam Hydro-mechanical Works",
      category: "hydro",
      location: "Karnataka",
      client: "Thungabhadra Steel Products",
      duration: "22 months",
      description: "Complete hydro-mechanical works at Gondi (Gosunda) Dam including fabrication and installation of gates, hoists, and associated equipment for irrigation and power generation.",
      image: "https://kimi-web-img.moonshot.cn/img/www.c-nct.com/d7ace124fb125c394743b80bdda7e5e66b90380c.jpg",
      icon: <FaWater className="w-6 h-6" />
    },
    {
      id: "cyclone-restoration",
      title: "HUDHUD Cyclone Restoration Works",
      category: "civil",
      location: "Visakhapatnam",
      client: "GE(I) NYC(V)",
      duration: "8 months",
      description: "Emergency restoration and upgradation works following HUDHUD cyclone damage. Included roofing, cladding rehabilitation, and structural repairs for naval facilities.",
      image: "https://kimi-web-img.moonshot.cn/img/theshawgrp.com/e3b475195e57ae1fbff7b64f433b45657b748108.jpg",
      icon: <FaHardHat className="w-6 h-6" />
    }
  ],
  ongoing: [
    {
      id: "rambilli-security",
      title: "Naval Station Rambilli Security Infrastructure",
      category: "defence",
      location: "Rambilli",
      client: "Indian Navy",
      progress: 65,
      description: "Provision of security fencing, watch towers, RCC storm drain, and allied works under WP-10B at Naval Station Rambilli near Visakhapatnam for enhanced security.",
      image: "https://kimi-web-img.moonshot.cn/img/veritassteel.com/4080d27e11c4ebe09ac275c11475681a26f39a80.jpg",
      icon: <FaShieldAlt className="w-6 h-6" />
    },
    {
      id: "aob-road",
      title: "AOB Rambilli Road Infrastructure",
      category: "civil",
      location: "Rambilli",
      client: "Indian Navy",
      progress: 45,
      description: "Repair to main road and internal road with allied road services at AOB Rambilli, Visakhapatnam including drainage, signage, and road furniture works.",
      image: "https://kimi-web-img.moonshot.cn/img/ecms.emech.com/cf0ab94e8286e13b83c7385c2ddf3c74172f601c.png",
      icon: <FaHardHat className="w-6 h-6" />
    },
    {
      id: "crane-upgradation",
      title: "Crane System Upgradation",
      category: "industrial",
      location: "Visakhapatnam",
      client: "GE(I) NYC(V)",
      progress: 30,
      description: "Special repairs and replacement of drives, braking systems, gear boxes, and other crane components at various locations under GE(I) NYC(V) jurisdiction.",
      image: "https://kimi-web-img.moonshot.cn/img/www.steel-structurewarehouse.com/5c3e34c14d90a640aea00779059e887d47fb71ff.jpg",
      icon: <FaIndustry className="w-6 h-6" />
    }
  ]
};

const categoryColors = {
  defence: "from-blue-600 to-cyan-600",
  hydro: "from-amber-500 to-orange-500",
  industrial: "from-emerald-500 to-green-500",
  civil: "from-purple-500 to-pink-500",
  ongoing: "from-red-500 to-pink-500"
};

const categoryLabels = {
  defence: "Defence & Marine",
  hydro: "Hydro Power",
  industrial: "Industrial",
  civil: "Civil Works",
  ongoing: "Ongoing"
};

export default function ProjectsPortfolio() {
  const [activeFilter, setActiveFilter] = useState("all");
  const [selectedProject, setSelectedProject] = useState(null);

  const filteredProjects = {
    completed: projectsData.completed.filter(project => 
      activeFilter === "all" || project.category === activeFilter
    ),
    ongoing: projectsData.ongoing.filter(project =>
      activeFilter === "all" || project.category === activeFilter || activeFilter === "ongoing"
    )
  };

  const openProjectModal = (projectId) => {
    const allProjects = [...projectsData.completed, ...projectsData.ongoing];
    const project = allProjects.find(p => p.id === projectId);
    setSelectedProject(project);
  };

  const closeProjectModal = () => {
    setSelectedProject(null);
  };

  return (
    <>
      {/* Hero Section */}
      <section className="pt-24 pb-16 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-white/10"></div>
        <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-blue-500"></div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center text-white">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              Project <span className="text-amber-400">Portfolio</span>
            </h1>
            <p className="text-xl md:text-2xl text-gray-300 max-w-4xl mx-auto leading-relaxed">
              Four decades of engineering excellence showcased through landmark
              projects across defence, hydro power, marine infrastructure, and
              industrial sectors.
            </p>
          </div>
        </div>
      </section>

      {/* Project Filters */}
      <section className="py-12 bg-white border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-8">
            <h2 className="text-2xl font-bold text-slate-900 mb-4">
              Filter Projects
            </h2>
            <div className="flex flex-wrap justify-center gap-4">
              {["all", "defence", "hydro", "industrial", "civil", "ongoing"].map((filter) => (
                <button
                  key={filter}
                  onClick={() => setActiveFilter(filter)}
                  className={`px-6 py-3 rounded-xl font-semibold transition-all duration-300 ${
                    activeFilter === filter
                      ? "bg-gradient-to-r from-amber-500 to-red-500 text-white shadow-lg"
                      : "bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200"
                  }`}
                >
                  {filter === "all" ? "All Projects" : categoryLabels[filter]}
                </button>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Completed Projects */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Completed <span className="text-blue-600">Projects</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto">
              A showcase of our successfully delivered projects demonstrating
              expertise across diverse engineering disciplines and sectors.
            </p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {filteredProjects.completed.map((project) => (
              <ProjectCard 
                key={project.id} 
                project={project} 
                onViewDetails={openProjectModal}
                type="completed"
              />
            ))}
          </div>
        </div>
      </section>

      {/* Ongoing Projects */}
      <section className="py-20 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Ongoing <span className="text-amber-600">Projects</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto">
              Current projects under execution demonstrating our continued
              commitment to excellence and expanding capabilities.
            </p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {filteredProjects.ongoing.map((project) => (
              <ProjectCard 
                key={project.id} 
                project={project} 
                onViewDetails={openProjectModal}
                type="ongoing"
              />
            ))}
          </div>
        </div>
      </section>

      {/* Project Statistics */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Project Success <span className="text-green-600">Metrics</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto">
              Our track record speaks for itself with consistent delivery excellence
              and client satisfaction across diverse project categories.
            </p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              { value: "500+", label: "Projects Completed", sublabel: "Across all sectors", color: "text-orange-600", icon: <FaCheckCircle className="w-8 h-8" /> },
              { value: "95%", label: "On-Time Delivery", sublabel: "Percentage rate", color: "text-blue-600", icon: <FaClock className="w-8 h-8" /> },
              { value: "50+", label: "Satisfied Clients", sublabel: "Including repeat customers", color: "text-green-600", icon: <FaUserTie className="w-8 h-8" /> },
              { value: "100%", label: "Safety Record", sublabel: "Zero major incidents", color: "text-purple-600", icon: <FaAward className="w-8 h-8" /> }
            ].map((stat, index) => (
              <div key={index} className="text-center bg-white border border-slate-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
               
                <div className="text-slate-800 font-semibold mb-2">
                  {stat.label}
                </div>
                <div className="text-slate-600 text-sm">
                  {stat.sublabel}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Call to Action */}
      <section className="py-20 bg-slate-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
            Ready to Start Your Next Project?
          </h2>
          <p className="text-xl text-slate-300 mb-12 max-w-3xl mx-auto">
            Join our list of satisfied clients and experience the difference that
            40+ years of engineering excellence can make for your project.
          </p>
          <div className="flex flex-col sm:flex-row gap-6 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              <FaRocket className="w-5 h-5" />
              Get Project Quote
            </Link>
            <Link
              href="/capabilities"
              className="inline-flex items-center gap-3 bg-transparent border-2 border-white text-white hover:bg-white hover:text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300"
            >
              <FaTools className="w-5 h-5" />
              Our Services
            </Link>
          </div>
        </div>
      </section>

      {/* Project Details Modal */}
      {selectedProject && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-4xl max-h-[90vh] overflow-y-auto w-full">
            <div className="p-8">
              <div className="flex justify-between items-center mb-6">
                <h3 className="text-2xl font-bold text-slate-900">
                  {selectedProject.title}
                </h3>
                <button
                  onClick={closeProjectModal}
                  className="text-slate-500 hover:text-slate-700 transition-colors p-2 hover:bg-slate-100 rounded-lg"
                >
                  <FaTimes className="w-6 h-6" />
                </button>
              </div>
              
              <div className="relative h-64 mb-6 rounded-xl overflow-hidden border border-slate-200">
                <Image
                  src={selectedProject.image}
                  alt={selectedProject.title}
                  fill
                  className="object-cover"
                />
              </div>
              
              <div className="grid md:grid-cols-2 gap-6 mb-6">
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <FaUserTie className="w-5 h-5 text-slate-400" />
                    <div>
                      <div className="text-slate-700 font-medium">Client:</div>
                      <div className="text-slate-900">{selectedProject.client}</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <FaMapMarkerAlt className="w-5 h-5 text-slate-400" />
                    <div>
                      <div className="text-slate-700 font-medium">Location:</div>
                      <div className="text-slate-900">{selectedProject.location}</div>
                    </div>
                  </div>
                  
                  {'duration' in selectedProject && (
                    <div className="flex items-center gap-3">
                      <FaCalendarAlt className="w-5 h-5 text-slate-400" />
                      <div>
                        <div className="text-slate-700 font-medium">Duration:</div>
                        <div className="text-slate-900">{selectedProject.duration}</div>
                      </div>
                    </div>
                  )}
                  {'progress' in selectedProject && (
                    <div className="flex items-center gap-3">
                      <HiOutlineTrendingUp className="w-5 h-5 text-slate-400" />
                      <div>
                        <div className="text-slate-700 font-medium">Progress:</div>
                        <div className="text-green-600 font-semibold">{selectedProject.progress}% Complete</div>
                      </div>
                    </div>
                  )}
                </div>
                
                <div>
                  <h4 className="font-semibold text-slate-900 mb-3 flex items-center gap-2">
                    <HiOutlineClipboardCheck className="w-5 h-5" />
                    Project Description
                  </h4>
                  <p className="text-slate-600 leading-relaxed">
                    {selectedProject.description}
                  </p>
                </div>
              </div>
              
              {'progress' in selectedProject && (
                <div className="mb-6">
                  <div className="flex justify-between text-sm text-slate-600 mb-2">
                    <span>Project Progress</span>
                    <span>{selectedProject.progress}%</span>
                  </div>
                  <div className="bg-slate-200 rounded-full h-3">
                    <div
                      className="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500"
                      style={{ width: `${selectedProject.progress}%` }}
                    />
                  </div>
                </div>
              )}
              
              <button
                onClick={closeProjectModal}
                className="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2"
              >
                <FaTimes className="w-4 h-4" />
                Close Details
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}

// Project Card Component
const ProjectCard = ({ project, onViewDetails, type }) => {
  return (
    <div className="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
      <div className="relative h-48 overflow-hidden">
        <Image
          src={project.image}
          alt={project.title}
          fill
          className="object-cover transition-transform duration-500 hover:scale-110"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" />
        <div className="absolute top-4 left-4">
          <span className={`inline-flex items-center gap-2 bg-gradient-to-r ${categoryColors[project.category]} text-white px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm`}>
            {project.icon}
            {categoryLabels[project.category]}
          </span>
        </div>
        <div className="absolute top-4 right-4">
          <span className={`inline-flex items-center gap-2 bg-gradient-to-r ${
            type === 'completed' ? 'from-green-500 to-emerald-500' : 'from-orange-500 to-red-500'
          } text-white px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm`}>
            {type === 'completed' ? <FaCheckCircle className="w-4 h-4" /> : <FaClock className="w-4 h-4" />}
            {type === 'completed' ? 'Completed' : 'Ongoing'}
          </span>
        </div>
        <div className="absolute bottom-4 left-4 text-white text-sm backdrop-blur-sm bg-black/30 px-3 py-1 rounded-lg flex items-center gap-2">
          <FaMapMarkerAlt className="w-3 h-3" />
          {project.location}
        </div>
      </div>
      
      <div className="p-6">
        <h3 className="text-xl font-bold text-slate-900 mb-3 line-clamp-2">
          {project.title}
        </h3>
        <p className="text-slate-600 mb-4 text-sm leading-relaxed line-clamp-3">
          {project.description}
        </p>
        
        <div className="space-y-3 mb-4">
          <div className="flex justify-between text-sm">
            <span className="text-slate-700 font-medium flex items-center gap-2">
              <FaUserTie className="w-4 h-4" />
              Client:
            </span>
            <span className="text-slate-900">{project.client}</span>
          </div>
   
          {'duration' in project && (
            <div className="flex justify-between text-sm">
              <span className="text-slate-700 font-medium flex items-center gap-2">
                <FaCalendarAlt className="w-4 h-4" />
                Duration:
              </span>
              <span className="text-slate-900">{project.duration}</span>
            </div>
          )}
          {'progress' in project && (
            <div className="flex justify-between text-sm">
              <span className="text-slate-700 font-medium flex items-center gap-2">
                <FaChartLine className="w-4 h-4" />
                Progress:
              </span>
              <span className="text-green-600 font-semibold">{project.progress}% Complete</span>
            </div>
          )}
        </div>

        {'progress' in project && (
          <div className="mb-4">
            <div className="flex justify-between text-sm text-slate-600 mb-2">
              <span>Project Progress</span>
              <span>{project.progress}%</span>
            </div>
            <div className="bg-slate-200 rounded-full h-2">
              <div
                className="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500"
                style={{ width: `${project.progress}%` }}
              />
            </div>
          </div>
        )}
        
        <button
          onClick={() => onViewDetails(project.id)}
          className="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2"
        >
          <HiOutlineClipboardCheck className="w-4 h-4" />
          View Details
        </button>
      </div>
    </div>
  );
};