import { Component, inject, ViewChild, ElementRef } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { environment } from '../../environments/environment';

interface FaqItem {
  id: string;
  category: string;
  question: string;
  answer: string;
  features?: string[];
  steps?: string[];
  notes?: string;
  isExpanded?: boolean;
}

@Component({
  selector: 'app-help-and-support',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './help-and-support.html',
  styleUrl: './help-and-support.scss',
})
export class HelpAndSupport {
  public env = environment;
  private authService = inject(AuthService);
  activeTab = 'general';
  searchQuery = '';

  @ViewChild('faqSection') faqSection!: ElementRef;
  @ViewChild('userGuideSection') userGuideSection?: ElementRef;
  @ViewChild('adminGuideSection') adminGuideSection?: ElementRef;
  @ViewChild('contactSection') contactSection!: ElementRef;
  @ViewChild('contributorsSection') contributorsSection!: ElementRef;

  // Admin URLs
  userManagementGuideURL = 'https://youtu.be/kWE5TtSwL7Y?si=K6LLYkgY3iRg7syC';
  requestApprovalGuideURL = 'https://youtu.be/ABpkMH3EvwI?si=e7ze9fnd0zAMgPeB';
  adminPlaylistURL = 'https://www.youtube.com/playlist?list=PL9WX_LsJ2S4ub3kqM2QcZSxp46hIeMZ2e';

  // User URLs
  supplyRequestGuideURL = 'https://youtu.be/RT3SWmA4CnA?si=YewkBEfLSqI7fS-D';
  trackRequestGuideURL = 'https://youtu.be/QVjd2sepO68?si=URznYZyqpBh-l01e';
  userPlaylistURL = 'https://www.youtube.com/playlist?list=PL9WX_LsJ2S4tJb1UHMJ4DyqgEugcZIaQj';


  allFaqs: FaqItem[] = [
    {
      id: 'faq1',
      category: 'general',
      question: 'What is SMIS?',
      answer: 'The Supply Management Information System (SMIS) is a comprehensive platform designed to streamline inventory management, supply requests, and distribution processes within the organization.',
      features: ['Centralized inventory tracking', 'Automated request processing', 'Real-time status updates', 'Comprehensive reporting tools'],
      isExpanded: true
    },
    {
      id: 'faq2',
      category: 'general',
      question: 'Who can access the system?',
      answer: 'The system is accessible to:',
      features: [
        'System Administrator: Manages the overall system, users, and configurations',
        'Inventory Managers: Handle inventory tracking and supply distribution',
        'Department Heads: Review and approve supply requests',
        'Authorized Personnel: Staff with specific permissions based on their role'
      ],
      notes: 'Each user type has different permissions and access levels tailored to their responsibilities.'
    },
    {
      id: 'faq3',
      category: 'general',
      question: 'What are the system requirements?',
      answer: 'SMIS is a web-based application that works on most modern devices. Recommended requirements:',
      features: [
        'Browser: Chrome (latest), Edge (latest)',
        'Internet Connection: Stable connection',
        'Screen Resolution: Minimum 1366 x 768'
      ],
      notes: 'For optimal performance, ensure your browser is updated to the latest version.'
    },
    {
      id: 'faq4',
      category: 'account',
      question: 'How do I manage user accounts?',
      answer: 'To manage user accounts:',
      steps: [
        'Navigate to "User Management" in the sidebar',
        'View all registered users and their status',
        'Use the "Add User" button to create new accounts',
        'Click on any user to edit their information or change permissions',
        'Use the activate/deactivate options to control access'
      ],
      notes: 'Remember to assign appropriate roles based on user responsibilities.'
    },
    {
      id: 'faq5',
      category: 'account',
      question: 'How to reset your password?',
      answer: 'To reset your password:',
      steps: [
        'Go to "Forget Password"',
        'Put your email',
        'Click "send reset link"',
        'Check your email',
        'Click the link to reset your password.'
      ],
      notes: 'Note: You have 10 mins to do so if fail to change, request another link to reset.'
    },
    {
      id: 'faq6',
      category: 'orders',
      question: 'How do I review and approve requests?',
      answer: 'To review and approve supply requests:',
      steps: [
        'Navigate to "Orders" → "Pending" to see all pending requests',
        'Click on a request to view detailed information',
        'Review the requested items, quantities, and justification',
        'Check inventory availability',
        'Click "Approve" or "Disapprove" with appropriate comments'
      ],
      notes: 'Approved requests will automatically move to the "Approved" section for processing.'
    },
    {
      id: 'faq7',
      category: 'orders',
      question: 'How do I manage inventory levels?',
      answer: 'To manage inventory:',
      steps: [
        'Access the inventory management section',
        'Monitor stock levels and set reorder points',
        'Add new items to the inventory catalog',
        'Update quantities when new supplies arrive',
        'Generate reports on inventory usage and trends'
      ],
      notes: 'Regular monitoring helps prevent stockouts and ensures smooth operations.'
    },
    {
      id: 'faq8',
      category: 'orders',
      question: 'How do I generate reports?',
      answer: 'To generate reports:',
      steps: [
        'Navigate to the "Reports" section in the sidebar',
        'Select the type of report you need (inventory, requests, usage, etc.)',
        'Set the date range and filter criteria',
        'Choose export format (PDF, Excel, etc.)',
        'Click "Generate Report"'
      ],
      notes: 'Reports help track system usage, inventory trends, and departmental needs.'
    },
    {
      id: 'faq9',
      category: 'technical',
      question: 'Common Issues & Solutions',
      answer: 'Login Issues:',
      features: [
        'Verify your username and password',
        'Clear browser cache and cookies',
        'Use the "Forgot Password" feature to reset',
        'Ensure Caps Lock is off',
        'Contact system administrator if issues persist'
      ],
      notes: 'System Performance: Check connection, clear cache (Ctrl+F5), use Chrome/Edge, close extra tabs.'
    },
    {
      id: 'faq10',
      category: 'technical',
      question: 'Browser Compatibility',
      answer: 'SMIS works best with the following browsers:',
      features: [
        'Google Chrome (Version 90+)',
        'Microsoft Edge (Version 90+)'
      ],
      notes: 'If you\'re experiencing issues, try updating your browser to the latest version.'
    }
  ];

  userFaqs: FaqItem[] = [
    {
      id: 'ufaq1',
      category: 'account',
      question: 'How do I reset my password?',
      answer: 'If you have forgotten your password, you can reset it by following these steps:',
      steps: [
        'On the Login page, click on the "Forgot Password?" link.',
        'Enter your registered email address.',
        'Check your email for a password reset link.',
        'Click the link and enter your new password.',
        'Log in with your new credentials.'
      ],
      notes: 'Note: The reset link is valid for 10 minutes. If it expires, you will need to request a new one.'
    },
    {
      id: 'ufaq2',
      category: 'account',
      question: 'How do I update my password?',
      answer: 'To update your password while logged in:',
      steps: [
        'Navigate to the top navigation bar.',
        'Click on your profile name/icon to open the dropdown menu.',
        'Select "Account Settings".',
        'In the "Change Password" section, enter your current password.',
        'Enter and confirm your new password.',
        'Click "Save Changes" to save changes.'
      ]
    },
    {
      id: 'ufaq3',
      category: 'orders',
      question: 'How do I create a new supply request?',
      answer: 'To submit a new request for supplies:',
      steps: [
        'Navigate to "Create Request" in the sidebar.',
        'Select the items you need from the available inventory.',
        'Specify the quantity for each item.',
        'Provide a justification for your request.',
        'Review your items and click "Submit Request".'
      ]
    },
    {
      id: 'ufaq4',
      category: 'orders',
      question: 'How do I track my request status?',
      answer: 'You can monitor the progress of your requests in real-time:',
      steps: [
        'Navigate to the "Orders" section in the sidebar.',
        'Select "Pending" to see requests awaiting approval.',
        'Select "Approved" to see requests ready for release.',
        'Select "Disapproved" to see rejected requests and their reasons.',
        'Check your notifications (bell icon) for instant updates.'
      ]
    },
    {
      id: 'ufaq5',
      category: 'orders',
      question: 'Can I modify or Cancel my request?',
      answer: 'Currently, requests cannot be modified or cancelled directly by the user once they have been submitted.',
      notes: 'If you need to make changes or cancel a request, please contact the Property Custodian Office or your system administrator as soon as possible.'
    },
    {
      id: 'ufaq6',
      category: 'technical',
      question: 'Common Issues & Solutions',
      answer: 'If you encounter issues while using the system, try these solutions:',
      notes: `<strong>Login Issues:</strong><br>
              • Verify your username and password<br>
              • Clear browser cache and cookies<br>
              • Use the "Forgot Password" feature to reset<br>
              • Ensure Caps Lock is off<br>
              • Contact system administrator if issues persist<br><br>
              <strong>System Performance:</strong><br>
              • Check your internet connection<br>
              • Clear browser cache (press Ctrl+F5)<br>
              • Use recommended browsers (Chrome, Edge)<br>
              • Close unnecessary browser tabs<br>
              • Try disabling browser extensions temporarily`
    }
  ];

  get filteredFaqs(): FaqItem[] {
    let activeFaqs: FaqItem[] = [];

    if (this.isAdmin) {
      activeFaqs = this.allFaqs;
    } else {
      // For regular users: Show General + Technical (except admin version of common issues) + User-specific FAQs
      const sharedFaqs = this.allFaqs.filter(faq => 
        faq.category === 'general' || 
        (faq.category === 'technical' && faq.id !== 'faq9')
      );
      activeFaqs = [...sharedFaqs, ...this.userFaqs];
    }

    if (!this.searchQuery) {
      return activeFaqs.filter(faq => faq.category === this.activeTab);
    }
    const query = this.searchQuery.toLowerCase();
    return activeFaqs.filter(faq => 
      faq.question.toLowerCase().includes(query) || 
      faq.answer.toLowerCase().includes(query) ||
      (faq.features && faq.features.some(f => f.toLowerCase().includes(query))) ||
      (faq.steps && faq.steps.some(s => s.toLowerCase().includes(query)))
    );
  }

  get isAdmin(): boolean {
    const roleName = this.authService.currentUser()?.role?.role_name?.toLowerCase();
    return roleName === 'admin' || roleName === 'superadmin';
  }

  get isUser(): boolean {
    const roleName = this.authService.currentUser()?.role?.role_name?.toLowerCase();
    return roleName === 'user';
  }

  scrollToSection(section: 'faq' | 'user' | 'admin' | 'contact' | 'contributors') {
    let element: ElementRef | undefined;
    
    if (section === 'faq') element = this.faqSection;
    else if (section === 'user') element = this.userGuideSection;
    else if (section === 'admin') element = this.adminGuideSection;
    else if (section === 'contact') element = this.contactSection;
    else if (section === 'contributors') element = this.contributorsSection;

    if (element) {
      element.nativeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  toggleFaq(faq: FaqItem) {
    const targetState = !faq.isExpanded;
    // Close all FAQs in both lists first
    this.allFaqs.forEach(item => item.isExpanded = false);
    this.userFaqs.forEach(item => item.isExpanded = false);
    // Set the clicked one to its new state
    faq.isExpanded = targetState;
  }

  goToUserManagementGuideURL() {
    window.open(this.userManagementGuideURL, '_blank');
  }

  goToRequestApprovalGuideURL() {
    window.open(this.requestApprovalGuideURL, '_blank');
  }

  goToAdminPlaylistURL() { 
    window.open(this.adminPlaylistURL, '_blank');
  }

  goToSupplyRequestGuideURL() {
    window.open(this.supplyRequestGuideURL, '_blank');
  }

  goToTrackRequestGuideURL() {
    window.open(this.trackRequestGuideURL, '_blank');
  }

  goToUserPlaylistURL() {
    window.open(this.userPlaylistURL, '_blank');
  }
}
