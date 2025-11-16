"use client";
import React, { useState } from "react";
import Link from "next/link";
import {
  FaMapMarkerAlt,
  FaPhone,
  FaEnvelope,
  FaGlobe,
  FaClock,
  FaUser,
  FaBuilding,
  FaBriefcase,
  FaCalendar,
  FaMoneyBillWave,
  FaFileAlt,
  FaPaperPlane,
  FaWhatsapp,
  FaLinkedin,
  FaFacebook,
  FaTwitter
} from "react-icons/fa";
import {
  HiOutlineMail,
  HiOutlinePhone,
  HiOutlineCalendar,
  HiOutlineCash,
  HiOutlineClipboardList
} from "react-icons/hi";

function ContactPage() {
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    company: '',
    designation: '',
    projectType: '',
    budget: '',
    timeline: '',
    description: '',
    contactMethod: ''
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Handle form submission here
    console.log('Form submitted:', formData);
    // You can add your form submission logic here
  };

  const contactInfo = [
    {
      icon: <FaMapMarkerAlt className="w-6 h-6" />,
      title: "Office Location",
      details: [
        "Head Office",
        "# 38-19-14/3, Jyothi Nagar",
        "Marripalem, Visakhapatnam - 530018",
        "Andhra Pradesh, India"
      ],
      gradient: "from-blue-500 to-cyan-500"
    },
    {
      icon: <FaPhone className="w-6 h-6" />,
      title: "Phone Numbers",
      details: [
        "+91 9515631221",
        "+91 7382074773",
        "Mon-Fri: 9:00 AM - 6:00 PM"
      ],
      gradient: "from-amber-500 to-orange-500"
    },
    {
      icon: <FaEnvelope className="w-6 h-6" />,
      title: "Email & Web",
      details: [
        "mgrajeevco@gmail.com",
        "www.mgrajeevco.com",
        "24/7 Online Presence"
      ],
      gradient: "from-purple-500 to-pink-500"
    }
  ];

  const keyContacts = [
    {
      initials: "SR",
      name: "S. Rukmani",
      role: "Managing Partner",
      contact: "+91 9515631221",
      gradient: "from-blue-500 to-cyan-500"
    },
    {
      initials: "RGN",
      name: "R. Goutham Nair",
      role: "Director Projects",
      contact: "+91 7382074773",
      gradient: "from-amber-500 to-orange-500"
    },
    {
      initials: "PPN",
      name: "P Padma Nair",
      role: "Administration Head",
      contact: "mgrajeevco@gmail.com",
      gradient: "from-purple-500 to-pink-500"
    }
  ];

  const businessHours = [
    { day: "Monday - Friday", hours: "9:00 AM - 6:00 PM" },
    { day: "Saturday", hours: "9:00 AM - 2:00 PM" },
    { day: "Sunday", hours: "Closed" }
  ];

  const faqs = [
    {
      question: "What types of projects do you undertake?",
      answer: "We specialize in hydro-mechanical works, heavy structural fabrication, marine & dockyard infrastructure, surface preparation & coatings, civil engineering, and electrical & mechanical works. We serve defence, hydro power, heavy industry, and public sectors."
    },
    {
      question: "What is your typical project timeline?",
      answer: "Project timelines vary based on scope and complexity. Small projects may take 1-6 months, medium projects 6-12 months, and large infrastructure projects 12+ months. We provide detailed timelines during the consultation phase."
    },
    {
      question: "Do you provide project consultation?",
      answer: "Yes, we offer comprehensive project consultation including feasibility studies, technical specifications, cost estimation, and project planning. Our experienced team can help optimize your project requirements."
    },
    {
      question: "What certifications do you hold?",
      answer: "We are ISO 9001:2008 certified for Quality Management Systems. We are registered with DGNP(V), Naval Dockyard, Ship Building Centre, and comply with MES/Indian Navy specifications and BIS/IS/ASME standards."
    },
    {
      question: "How do you ensure project quality?",
      answer: "We maintain rigorous quality control through documented ITPs, WPS/PQRs, calibrated tools, digital QA/QC systems, and compliance with international standards. Our quality assurance processes ensure consistent delivery excellence."
    }
  ];

  const projectTypes = [
    "Hydro-mechanical Works",
    "Heavy Structural Fabrication",
    "Marine & Dockyard Works",
    "Surface Preparation & Coatings",
    "Civil Engineering Works",
    "Electrical & Mechanical Works",
    "Operations & Maintenance",
    "Other"
  ];

  const budgetRanges = [
    "Below ₹5 Crores",
    "₹5 - ₹15 Crores",
    "₹15 - ₹50 Crores",
    "Above ₹50 Crores"
  ];

  const timelines = [
    "Immediate (Within 1 month)",
    "Short-term (1-6 months)",
    "Medium-term (6-12 months)",
    "Long-term (Above 12 months)"
  ];

  return (
    <>
      {/* Hero Section */}
      <section className="pt-24 pb-16 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-white/10"></div>
        <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-blue-500"></div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center text-white">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              Contact <span className="text-amber-400">Us</span>
            </h1>
            <p className="text-xl md:text-2xl text-gray-300 max-w-4xl mx-auto leading-relaxed">
              Ready to discuss your next project? Get in touch with our
              experienced team for expert consultation and competitive project
              quotes.
            </p>
          </div>
        </div>
      </section>

      {/* Contact Information */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Get In <span className="text-blue-600">Touch</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Multiple ways to connect with our team for project inquiries,
              consultations, and business partnerships.
            </p>
          </div>
          
          <div className="grid lg:grid-cols-3 gap-8 mb-16">
            {contactInfo.map((info, index) => (
              <div key={index} className="group bg-white border border-slate-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 text-center">
                <div className={`inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r ${info.gradient} rounded-2xl mb-6 mx-auto group-hover:scale-110 transition-transform duration-300`}>
                  {info.icon}
                </div>
                <h3 className="text-xl font-bold text-slate-900 mb-4">
                  {info.title}
                </h3>
                <div className="text-slate-600 space-y-2">
                  {info.details.map((detail, idx) => (
                    <p key={idx} className={idx === 0 ? "font-semibold" : ""}>
                      {detail.includes('@') || detail.includes('www.') || detail.includes('+91') ? (
                        <a 
                          href={detail.includes('@') ? `mailto:${detail}` : detail.includes('www.') ? `http://${detail}` : `tel:${detail}`}
                          className="hover:text-orange-600 transition-colors duration-300"
                        >
                          {detail}
                        </a>
                      ) : (
                        detail
                      )}
                    </p>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Contact Form and Map */}
      <section className="py-20 bg-slate-50 relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div>
              <h2 className="text-3xl font-bold text-slate-900 mb-8">
                Send Us a <span className="text-amber-600">Message</span>
              </h2>
              <form onSubmit={handleSubmit} className="space-y-6">
                {/* Personal Information */}
                <div className="grid md:grid-cols-2 gap-6">
                  <div>
                    <label htmlFor="firstName" className="block text-slate-700 font-semibold mb-2">
                      First Name *
                    </label>
                    <div className="relative">
                      <FaUser className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <input
                        type="text"
                        id="firstName"
                        name="firstName"
                        required
                        value={formData.firstName}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white"
                        placeholder="Enter your first name"
                      />
                    </div>
                  </div>
                  <div>
                    <label htmlFor="lastName" className="block text-slate-700 font-semibold mb-2">
                      Last Name *
                    </label>
                    <div className="relative">
                      <FaUser className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <input
                        type="text"
                        id="lastName"
                        name="lastName"
                        required
                        value={formData.lastName}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white"
                        placeholder="Enter your last name"
                      />
                    </div>
                  </div>
                </div>

                {/* Contact Information */}
                <div className="grid md:grid-cols-2 gap-6">
                  <div>
                    <label htmlFor="email" className="block text-slate-700 font-semibold mb-2">
                      Email Address *
                    </label>
                    <div className="relative">
                      <HiOutlineMail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        value={formData.email}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white"
                        placeholder="your.email@example.com"
                      />
                    </div>
                  </div>
                  <div>
                    <label htmlFor="phone" className="block text-slate-700 font-semibold mb-2">
                      Phone Number
                    </label>
                    <div className="relative">
                      <HiOutlinePhone className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value={formData.phone}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white"
                        placeholder="+91 XXXXXXXXXX"
                      />
                    </div>
                  </div>
                </div>

                {/* Company Information */}
                <div className="grid md:grid-cols-2 gap-6">
                  <div>
                    <label htmlFor="company" className="block text-slate-700 font-semibold mb-2">
                      Company/Organization
                    </label>
                    <div className="relative">
                      <FaBuilding className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <input
                        type="text"
                        id="company"
                        name="company"
                        value={formData.company}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white"
                        placeholder="Your company name"
                      />
                    </div>
                  </div>
                  <div>
                    <label htmlFor="designation" className="block text-slate-700 font-semibold mb-2">
                      Designation
                    </label>
                    <div className="relative">
                      <FaBriefcase className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <input
                        type="text"
                        id="designation"
                        name="designation"
                        value={formData.designation}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white"
                        placeholder="Your position"
                      />
                    </div>
                  </div>
                </div>

                {/* Project Details */}
                <div className="grid md:grid-cols-3 gap-6">
                  <div>
                    <label htmlFor="projectType" className="block text-slate-700 font-semibold mb-2">
                      Project Type *
                    </label>
                    <div className="relative">
                      <HiOutlineClipboardList className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <select
                        id="projectType"
                        name="projectType"
                        required
                        value={formData.projectType}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white appearance-none"
                      >
                        <option value="">Select Type</option>
                        {projectTypes.map((type, index) => (
                          <option key={index} value={type.toLowerCase().replace(/\s+/g, '-')}>
                            {type}
                          </option>
                        ))}
                      </select>
                    </div>
                  </div>
                  <div>
                    <label htmlFor="budget" className="block text-slate-700 font-semibold mb-2">
                      Budget Range
                    </label>
                    <div className="relative">
                      <HiOutlineCash className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <select
                        id="budget"
                        name="budget"
                        value={formData.budget}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white appearance-none"
                      >
                        <option value="">Select Budget</option>
                        {budgetRanges.map((range, index) => (
                          <option key={index} value={range.toLowerCase().replace(/\s+/g, '-')}>
                            {range}
                          </option>
                        ))}
                      </select>
                    </div>
                  </div>
                  <div>
                    <label htmlFor="timeline" className="block text-slate-700 font-semibold mb-2">
                      Timeline
                    </label>
                    <div className="relative">
                      <HiOutlineCalendar className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5" />
                      <select
                        id="timeline"
                        name="timeline"
                        value={formData.timeline}
                        onChange={handleInputChange}
                        className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white appearance-none"
                      >
                        <option value="">Select Timeline</option>
                        {timelines.map((timeline, index) => (
                          <option key={index} value={timeline.toLowerCase().replace(/\s+/g, '-')}>
                            {timeline}
                          </option>
                        ))}
                      </select>
                    </div>
                  </div>
                </div>

                {/* Project Description */}
                <div>
                  <label htmlFor="description" className="block text-slate-700 font-semibold mb-2">
                    Project Description *
                  </label>
                  <div className="relative">
                    <FaFileAlt className="absolute left-3 top-3 text-slate-400 w-5 h-5" />
                    <textarea
                      id="description"
                      name="description"
                      rows={5}
                      required
                      value={formData.description}
                      onChange={handleInputChange}
                      placeholder="Please provide detailed information about your project requirements, scope, location, and any specific technical specifications..."
                      className="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-300 bg-white resize-none"
                    />
                  </div>
                </div>

                {/* Preferred Contact Method */}
                <div>
                  <label className="block text-slate-700 font-semibold mb-3">
                    Preferred Contact Method
                  </label>
                  <div className="flex flex-wrap gap-6">
                    {['email', 'phone', 'meeting'].map((method) => (
                      <label key={method} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="contactMethod"
                          value={method}
                          checked={formData.contactMethod === method}
                          onChange={handleInputChange}
                          className="mr-3 text-amber-500 focus:ring-amber-500"
                        />
                        <span className="text-slate-700 capitalize">{method}</span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Submit Button */}
                <div className="pt-4">
                  <button
                    type="submit"
                    className="w-full inline-flex items-center justify-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                  >
                    <FaPaperPlane className="w-5 h-5" />
                    Send Project Inquiry
                  </button>
                </div>
              </form>
            </div>

            {/* Map and Additional Info */}
            <div>
              <h2 className="text-3xl font-bold text-slate-900 mb-8">
                Our <span className="text-blue-600">Location</span>
              </h2>
              
              {/* Map */}
              <div className="mb-8 rounded-2xl overflow-hidden shadow-xl border border-slate-200">
                <div className="w-full h-64 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center">
                  <div className="text-white text-center">
                    <FaMapMarkerAlt className="w-12 h-12 mx-auto mb-4" />
                    <p className="text-lg font-semibold">Visakhapatnam Office</p>
                    <p className="text-sm opacity-90">Marripalem, Visakhapatnam - 530018</p>
                  </div>
                </div>
              </div>

              {/* Key Contacts */}
              <div className="bg-white border border-slate-200 p-6 rounded-2xl shadow-lg mb-6">
                <h3 className="text-xl font-bold text-slate-900 mb-4">
                  Key Contacts
                </h3>
                <div className="space-y-4">
                  {keyContacts.map((contact, index) => (
                    <div key={index} className="flex items-center space-x-4 group">
                      <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-r ${contact.gradient} rounded-2xl text-white font-bold group-hover:scale-110 transition-transform duration-300`}>
                        {contact.initials}
                      </div>
                      <div className="flex-1">
                        <p className="font-semibold text-slate-900">{contact.name}</p>
                        <p className="text-slate-600 text-sm">{contact.role}</p>
                        <p className="text-slate-600 text-sm">
                          <a 
                            href={contact.contact.includes('@') ? `mailto:${contact.contact}` : `tel:${contact.contact}`}
                            className="hover:text-amber-600 transition-colors duration-300"
                          >
                            {contact.contact}
                          </a>
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Business Hours */}
              <div className="bg-white border border-slate-200 p-6 rounded-2xl shadow-lg">
                <h3 className="text-xl font-bold text-slate-900 mb-4">
                  Business Hours
                </h3>
                <div className="space-y-3 text-slate-600">
                  {businessHours.map((schedule, index) => (
                    <div key={index} className="flex justify-between items-center">
                      <span className="font-medium">{schedule.day}</span>
                      <span className={`flex items-center gap-2 ${schedule.hours === 'Closed' ? 'text-red-500' : 'text-green-600'}`}>
                        <FaClock className="w-4 h-4" />
                        {schedule.hours}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section className="py-20 bg-white relative overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
              Frequently Asked <span className="text-blue-600">Questions</span>
            </h2>
            <div className="w-24 h-1 bg-amber-400 mx-auto mb-6"></div>
            <p className="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
              Common questions about our services, processes, and project
              engagement.
            </p>
          </div>
          
          <div className="max-w-4xl mx-auto space-y-6">
            {faqs.map((faq, index) => (
              <div key={index} className="group bg-slate-50 border border-slate-200 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <h3 className="text-xl font-bold text-slate-900 mb-3">
                  {faq.question}
                </h3>
                <p className="text-slate-600 leading-relaxed">
                  {faq.answer}
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
            Let's Build Something Great Together
          </h2>
          <p className="text-xl text-slate-300 mb-12 max-w-3xl mx-auto leading-relaxed">
            With 40+ years of engineering excellence and a commitment to
            quality, we're ready to bring your vision to reality.
          </p>
          <div className="flex flex-col sm:flex-row gap-6 justify-center">
            <a
              href="#contact-form"
              className="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              <FaPaperPlane className="w-5 h-5" />
              Start Your Project
            </a>
            <a
              href="tel:+919515631221"
              className="inline-flex items-center gap-3 bg-transparent border-2 border-white text-white hover:bg-white hover:text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300"
            >
              <FaPhone className="w-5 h-5" />
              Call Now
            </a>
          </div>
        </div>
      </section>
    </>
  );
}

export default ContactPage;