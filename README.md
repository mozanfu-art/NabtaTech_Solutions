# NabtaTech Solutions – Integrated Company IT Systems

*Simulated Company Integrated IT Infrastructure and Applications*

---

## Project Overview

This repository presents **NabtaTech Solutions**, a fully simulated company, through a comprehensive IT ecosystem combining **network infrastructure, virtual servers, and interconnected departmental applications**. The setup models real-world enterprise operations and demonstrates the integration of multiple functional systems.

The project includes:

- **Network and Infrastructure:** Departmental VLANs, inter-VLAN routing, trunking, and connectivity between switches, routers, and Linux virtual servers for core services.
- **Virtual Servers:** Ubuntu-based File Server, Web Server, and Database Server supporting internal applications and workflows.
- **Departmental Applications:** Secretary, Receptionist, Office Administration, HR, Finance, IT Support, and DevOps modules.
- **Interconnected Workflows:** Realistic operational scenarios linking all departments, simulating communication, task management, visitor handling, payroll, IT support, and system monitoring.

All configuration files, diagrams, IP addressing plans, and documentation are available in the shared Drive and GitHub repository to provide a complete and professional portfolio-ready demonstration.

---

## Project Components

### Network & Virtual Infrastructure
- Full company network design with VLAN segmentation and interconnectivity.
- Router configuration for department routing and access.
- Linux VMs hosting File, Web, and Database servers.
- Complete network documentation including IP addressing and topology diagrams.

### Secretary Module
- Task scheduling, meeting coordination, and internal notifications.
- Centralized management integrated with other departmental systems.

### Reception Module
- Visitor registration, appointment management, and guest tracking.
- Real-time updates shared with Secretary and Office Administration modules.

### Office Administration Module
- Employee records, attendance, and general administrative management.
- Interconnected with HR, Finance, Secretary, and Reception modules.

### HR & Finance Modules
- HR: Recruitment, onboarding, leave management, employee information tracking.
- Finance: Payroll, expense tracking, and financial reporting.
- Integrated data management with Office Administration and IT/DevOps systems.

### IT Support & DevOps Modules
- IT Support: Ticket management, device inventory, network monitoring.
- DevOps: Server deployment automation, application monitoring, and maintenance tasks.
- Ensures continuity of operations and cross-department support.

---

## Resources

- **Google Drive:** [Project Files, Demos, and Documentation](https://drive.google.com/drive/folders/1CQy_Z-U9J0OdYcqkS8MTRSWttD_PIffX?usp=sharing)  
- **GitHub Repository:** [NabtaTech_Solutions](https://github.com/mozanfu-art/NabtaTech_Solutions)

---

## System Requirements

- **Network Simulation:** Cisco Packet Tracer, GNS3, or physical switches/routers.
- **Virtualization:** VMware Workstation or VirtualBox.
- **Operating System:** Ubuntu 20.04+ for VMs.
- **Applications:** PHP, MySQL, JavaScript, Python (depending on module).
- **Tools:** SSH clients (PuTTY, WinSCP), web browsers for accessing applications.

---

## Setup Instructions

1. **Deploy Network Infrastructure**
   - Configure VLANs, trunking, and routing using `/network` scripts.
   - Verify connectivity and IP addressing.

2. **Setup Virtual Machines**
   - Import Ubuntu VMs from `/vms`.
   - Configure File, Web, and Database servers with assigned subnets.

3. **Deploy Departmental Applications**
   - Secretary, Receptionist, and Office Administration modules on Web Server.
   - HR, Finance, IT Support, and DevOps modules as per documentation.
   - Ensure database connections and inter-module integration.

4. **Verify Operations**
   - Test communication and workflow between all modules: Secretary ↔ Reception ↔ Office Administration ↔ HR ↔ Finance ↔ IT/DevOps.

---

## Usage

- Simulates **real-world company workflows** including task management, visitor handling, administrative operations, HR & payroll processing, IT support, and system automation.
- Demonstrates **enterprise IT system integration** and operational continuity in a controlled simulated environment.

---

## Contributing

This repository is intended as a **professional portfolio demonstration**. Contributions, improvements, or module expansions are welcome via pull requests.

---

## License

**not licensed for commercial deployment or production use**.

---

## Acknowledgments

- Cisco Packet Tracer / GNS3 for network simulation  
- Ubuntu for virtual server deployment  
- Inspired by enterprise IT infrastructure and administrative systems



