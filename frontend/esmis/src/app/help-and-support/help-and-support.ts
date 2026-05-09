import { Component, inject, ViewChild, ElementRef } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

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
  imports: [Sidebar, TopNav, CommonModule, FormsModule],
  templateUrl: './help-and-support.html',
  styleUrl: './help-and-support.scss',
})
export class HelpAndSupport {
  private authService = inject(AuthService);
  activeTab = 'general';
  searchQuery = '';

  @ViewChild('faqSection') faqSection!: ElementRef;
  @ViewChild('adminGuideSection') adminGuideSection?: ElementRef;
  @ViewChild('contactSection') contactSection!: ElementRef;
  @ViewChild('contributorsSection') contributorsSection!: ElementRef;

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
        'Browser: Chrome (latest), Firefox (latest), Edge (latest)',
        'Internet Connection: Stable broadband connection',
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
      notes: 'System Performance: Check connection, clear cache (Ctrl+F5), use Chrome/Firefox, close extra tabs.'
    },
    {
      id: 'faq10',
      category: 'technical',
      question: 'Browser Compatibility',
      answer: 'SMIS works best with the following browsers:',
      features: [
        'Google Chrome (Version 90+)',
        'Mozilla Firefox (Version 88+)',
        'Microsoft Edge (Version 90+)',
        'Safari (Version 14+)'
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
    }
  ];

  get filteredFaqs(): FaqItem[] {
    const activeFaqs = this.isAdmin ? this.allFaqs : this.userFaqs;

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

  scrollToSection(section: 'faq' | 'admin' | 'contact' | 'contributors') {
    let element: ElementRef | undefined;
    
    if (section === 'faq') element = this.faqSection;
    else if (section === 'admin') element = this.adminGuideSection;
    else if (section === 'contact') element = this.contactSection;
    else if (section === 'contributors') element = this.contributorsSection;

    if (element) {
      element.nativeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  toggleFaq(faq: FaqItem) {
    const targetState = !faq.isExpanded;
    // Close all FAQs first
    this.allFaqs.forEach(item => item.isExpanded = false);
    // Set the clicked one to its new state
    faq.isExpanded = targetState;
  }
}
