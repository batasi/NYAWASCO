{{-- resources/views/board-of-directors.blade.php --}}
@extends('layouts.app')

@section('title', 'Board of Directors - NYAWASCO')

@section('content')
    <!-- Hero Section -->
     <style>
        /* Add to your CSS file or <style> tag */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.shadow-soft {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
}
     </style>
  <!-- Corporate Executive Hero -->
<section class="relative bg-white pt-16 pb-12">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-green-500 to-blue-600"></div>
    <div class="absolute top-20 left-10 w-20 h-20 rounded-full bg-blue-50 opacity-50"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 rounded-full bg-green-50 opacity-30"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
            <!-- Text Content -->
            <div class="lg:w-2/3">
                <div class="inline-flex items-center px-4 py-2 bg-blue-50 rounded-lg mb-6">
                    <i class="fas fa-users text-blue-600 mr-3"></i>
                    <span class="text-sm font-semibold text-blue-700 uppercase tracking-wider">
                        Executive Leadership
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                    Strategic <span class="text-blue-600">Governance</span>
                    <br class="hidden lg:block">for Sustainable <span class="text-green-600">Water</span> Services
                </h1>

                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Meet the accomplished professionals who provide strategic oversight and governance
                    to ensure NYAWASCO delivers reliable, affordable, and sustainable water and
                    sanitation services to Nyamira County.
                </p>

                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-award text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Expert Leadership</div>
                            <div class="text-sm text-gray-500">Industry professionals</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Strong Governance</div>
                            <div class="text-sm text-gray-500">Ethical oversight</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="lg:w-1/3">
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Board At A Glance</h3>
                    <div class="space-y-6">


                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Years Avg. Experience</span>
                                <span class="text-2xl font-bold text-blue-700">15+</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <div class="text-sm text-gray-500 mb-2">Meeting Frequency</div>
                                <div class="text-lg font-semibold text-blue-700">Quarterly</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

  <!-- Board Members Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Our Board Members</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Experienced professionals committed to providing strategic direction and oversight for sustainable water and sanitation services
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Member 1: James Bundi Morwabe -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-40 h-40 mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                            <img src="/img/board-members/james-bundi.jpeg" alt="James Bundi Morwabe" class="w-full h-top object-cover">
                        </div>
                        <h3 class="text-2xl font-bold text-blue-700 mb-2">James Bundi Morwabe</h3>
                        <p class="text-blue-600 font-semibold mb-4">Board Member</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Academic Background</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>BA Degree, Second Class (HONS)</li>
                                <li>Specialist in evaluation monitoring and development</li>
                                <li>Three diplomas in social work, comparative management and public administration</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Career Highlights</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Member of Board of Governors Senator Kebaso Secondary School (2008-2013)</li>
                                <li>Member of Board of Governors Kebirigo High School (2016-2018)</li>
                                <li>Managing Director - Harbinger Insurance Brokers Ltd (2001-2010)</li>
                                <li>Director - Lake Victoria South Water Services Board</li>
                                <li>Director - National Water Conservation and Pipeline Corporation</li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Specialization</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Financial Management</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Human Resources</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Public Administration</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Governance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member 2: Dr. Eng. Caroline Mong'ina Matara -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-40 h-40 mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                            <img src="/img/board-members/caroline-matara.jpeg" alt="Dr. Eng. Caroline Mong'ina Matara" class="w-full h-full object-contain">
                        </div>
                        <h3 class="text-2xl font-bold text-blue-700 mb-2">Dr. Eng. Caroline Mong'ina Matara</h3>
                        <p class="text-blue-600 font-semibold mb-4">Board Member</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Qualifications</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>PhD in Civil Engineering (University of Nairobi)</li>
                                <li>MEng in Traffic & Transportation Engineering</li>
                                <li>BSc in Civil Engineering (University of Nairobi)</li>
                                <li>Registered Professional Engineer (EBK)</li>
                                <li>Corporate Member - Institution of Engineers of Kenya</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Current Position</h4>
                            <p class="text-gray-600 text-sm">
                                Lecturer, Department of Civil Engineering<br>
                                Multimedia University
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Professional Experience</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Tutorial Fellow - Technical University of Kenya</li>
                                <li>Engineer - Kenya Informal Settlements Improvement Projects</li>
                                <li>Assistant Engineer - Kenya Urban Roads Authority</li>
                                <li>Assistant Engineer - Nairobi Metropolitan Services Improvement Projects</li>
                                <li>Over 10 years in academia and civil engineering consultancy</li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Expertise</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Civil Engineering</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Infrastructure Design</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Transportation</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Project Supervision</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member 3: CPA Asenath Maobe, Ph.D. -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-40 h-40 mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                            <img src="/img/board-members/asenath-maobe.png" alt="CPA Asenath Maobe Ph.D." class="w-full h-tops object-cover">
                        </div>
                        <h3 class="text-2xl font-bold text-blue-700 mb-2">Dr. CPA Asenath Maobe, Ph.D.</h3>
                        <p class="text-blue-600 font-semibold mb-4">Board Member</p>
                    </div>

                    <div class="space-y-4">
                         <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Professional Profile</h4>
                            <p class="text-gray-600 text-sm">
                               Dr. CPA Asenath Maobe is a self-driven individual with over fifteen years of strong interdisciplinary experience in;
                                Administration, Research, gender works, operational risk management, accounting,
                                 auditing, training, education, education policy, investment banking, budgeting,
                                 and  leadership.

                            </p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Current Position</h4>
                            <p class="text-gray-600 text-sm">
                                County Chief Officer<br>
                                Finance and Accounting Services<br>
                                County Government of Nyamira
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Education</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Ph.D. in Finance</li>
                                <li>MBA in Finance</li>
                                <li>Postgraduate Degree in International Educational Policy</li>
                                <li>Bachelor of Commerce - Accounting (Hons)</li>
                                <li>Certified Public Accountant (CPA-K)</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Achievements & Recognition</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Best Post Doc Fellow of UNESCO - East China Normal University (2020)</li>
                                <li>Member - Institute of Certified Public Accountants of Kenya</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Other Roles</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Senior Lecturer - Kisii University</li>
                                <li>Chair - Institutional Scientific Ethics Review Committee</li>
                                <li>Involved in a Qual Case with University College London on carbon literacy levels among farmers in Kisii and Nyamira counties</li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Areas of Passion</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">Financial Literacy</span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">Climate Change</span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">Gender Issues</span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">Risk Management</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member 4: Victornah Kemunto Makori -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-40 h-40 mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                            <img src="/img/board-members/victorinah-makori.png" alt="Victornah Kemunto Makori" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-2xl font-bold text-blue-700 mb-2">Victornah Kemunto Makori</h3>
                        <p class="text-blue-600 font-semibold mb-4">Board Member</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Professional Profile</h4>
                            <p class="text-gray-600 text-sm">
                                Experienced Community Development Specialist with over ten years of professional practice
                                in sustainable development and social impact initiatives. Specializes in community-led
                                water, sanitation, and livelihood interventions.
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Education & Qualifications</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Bachelor of Arts in Human & Social Studies (Community Development)</li>
                                <li>Diploma in Social Work - Distinction</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Core Competencies</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">Community Mobilization</span>
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">WASH Programs</span>
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">Project Management</span>
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">Capacity Building</span>
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">Stakeholder Coordination</span>
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">MEL Systems</span>
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-full">Sustainable Livelihoods</span>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Professional Experience</h4>
                            <p class="text-gray-600 text-sm">
                                Worked with reputable development organizations including New Dawn Foundation & Trust,
                                We World Kenya Foundation, CARE International, YWCA, and Horizon Africa. Specializes in
                                water access, social inclusion, women's empowerment, and sustainable community systems.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Professional Approach</h4>
                            <p class="text-gray-600 text-sm">
                                Applies participatory and rights-based approaches ensuring projects are inclusive,
                                locally owned, and environmentally responsible. Skilled in aligning donor requirements
                                with community priorities.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member 5: Dr. Lugard Kaunda Ogaro -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-40 h-40 mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                            <img src="/img/board-members/lugard-ogaro.jpeg" alt="Dr. Lugard Kaunda Ogaro" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-2xl font-bold text-blue-700 mb-2">Dr. Lugard Kaunda Ogaro</h3>
                        <p class="text-blue-600 font-semibold mb-4">Board Member</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Education</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>PhD, Disaster Management & Sustainable Development - MMUST</li>
                                <li>MSc Civil Engineering (Environmental Health) - University of Nairobi</li>
                                <li>BSc Civil Engineering - JKUAT</li>
                                <li>Diploma, International Environmental Law - UNITAR</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Current Position</h4>
                            <p class="text-gray-600 text-sm">
                                Adjunct Lecturer<br>
                                Department of Disaster Management and Sustainable Development<br>
                                Masinde Muliro University of Science and Technology
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">International Experience</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>WASH Technical Advisor - Mercy Corps Global (Sudan, 2024-present)</li>
                                <li>Director of Programs - Mercy Corps Somalia (2020-2023)</li>
                                <li>Technical Manager - Danish Refugee Council Somalia (2018-2019)</li>
                                <li>Governance Manager - DAI-USAID KIWASH Kenya (2016-2018)</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Regional Experience</h4>
                            <p class="text-gray-600 text-sm">
                                Extensive experience in Somalia, Kenya, South Sudan, Sudan, with remote technical support to Ethiopia, Djibouti, Uganda, and Yemen.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Core Strengths</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Water Resource Management</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">WASH Governance</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Disaster Risk Reduction</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Infrastructure Design</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Policy Reform</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Emergency WASH</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Member 6: CO. Hon. Richard Onyinkwa -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-40 h-40 mb-4 rounded-full overflow-hidden border-4 border-blue-100">
                            <img src="/img/board-members/richard-onyinkwa.jpg" alt="CO. Hon. Richard Onyinkwa" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-2xl font-bold text-blue-700 mb-2 text-center">CO. Hon. Richard Onyinkwa</h3>
                        <p class="text-blue-600 font-semibold mb-4">Board Member</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Current Position</h4>
                            <p class="text-gray-600 text-sm">
                                County Chief Officer - Environment, Water, Irrigation & Sanitation

                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Education</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Masters Degree in Project Planning & Management - University of Nairobi</li>
                                <li>Bachelor of Law - Kisii University</li>
                                <li>Currently pursuing PhD - University of Nairobi</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Previous Roles</h4>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li> County Chief Officer
                                Economic Planning, Resource Mobilization and ICT
                                County Government of Nyamira
                                <li>Elected Member of County Assembly - Magombo Ward (2017-2022)</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Key Expertise</h4>
                            <p class="text-gray-600 text-sm">
                                Vast experience in management, supervision, and budgeting across various sectors.
                                Special focus on economic planning, resource mobilization, and ICT integration in
                                public service delivery.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-2">Areas of Specialization</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">Economic Planning</span>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">Resource Mobilization</span>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">Project Management</span>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">Public Sector Budgeting</span>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">ICT Strategy</span>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">Legislative Governance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

    <!-- Board Responsibilities Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Board Responsibilities</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Our Board of Directors provides strategic oversight and governance to ensure NYAWASCO delivers on its mandate
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Strategic Direction</h3>
                    <p class="text-gray-600">Setting long-term vision and strategic objectives</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Governance</h3>
                    <p class="text-gray-600">Ensuring compliance and ethical standards</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Performance Oversight</h3>
                    <p class="text-gray-600">Monitoring organizational performance</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-handshake text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Stakeholder Engagement</h3>
                    <p class="text-gray-600">Representing community and stakeholder interests</p>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('styles')
<style>
    .board-member-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .board-member-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .expertise-tag {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        margin: 2px;
    }
</style>
@endsection
