// Smooth scrolling for navigation links
document.addEventListener('DOMContentLoaded', function() {
    // Get all navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Add click event listeners for smooth scrolling
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            
            // Update active nav link
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Add navbar background on scroll
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(10, 10, 10, 0.98)';
        } else {
            navbar.style.background = 'rgba(10, 10, 10, 0.95)';
        }
    });

    // Add typing effect to hero title
    const heroTitle = document.querySelector('.hero-title');
    if (heroTitle) {
        const text = heroTitle.textContent;
        heroTitle.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                heroTitle.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        
        // Start typing effect after a short delay
        setTimeout(typeWriter, 500);
    }

    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe animated elements
    const animatedElements = document.querySelectorAll('.about-card, .stat-item, .status-card');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Update active nav link on scroll
    const sections = document.querySelectorAll('section[id]');
    
    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.pageYOffset >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
});

// Copy IP to clipboard function
function copyIP() {
    const ipText = '94.23.168.153:1285';
    navigator.clipboard.writeText(ipText).then(() => {
        // Show temporary feedback
        const serverIP = document.querySelector('.server-ip');
        const originalHTML = serverIP.innerHTML;
        serverIP.innerHTML = '<span class="copy-ip">Copied!</span><i class="fas fa-check ms-2"></i>';
        
        setTimeout(() => {
            serverIP.innerHTML = originalHTML;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = ipText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        // Show feedback
        const serverIP = document.querySelector('.server-ip');
        const originalHTML = serverIP.innerHTML;
        serverIP.innerHTML = '<span class="copy-ip">Copied!</span><i class="fas fa-check ms-2"></i>';
        
        setTimeout(() => {
            serverIP.innerHTML = originalHTML;
        }, 2000);
    });
}

// Connect to server function
function connectToServer() {
    const serverIP = '94.23.168.153:1285';
    
    // Try to open SA-MP protocol link
    const sampLink = `samp://${serverIP}`;
    
    // Create a temporary link and click it
    const link = document.createElement('a');
    link.href = sampLink;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show notification
    showNotification('Attempting to connect to server...', 'info');
    
    // Fallback: show manual instructions after a delay
    setTimeout(() => {
        showNotification(`If SA-MP didn't open automatically, please copy this IP: ${serverIP}`, 'warning');
    }, 3000);
}

// Show notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification alert alert-${type === 'info' ? 'primary' : type === 'warning' ? 'warning' : 'success'}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
        padding: 1rem;
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.9);
        border: 1px solid var(--primary-color);
        color: white;
        backdrop-filter: blur(10px);
        animation: slideInRight 0.3s ease;
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'info' ? 'info-circle' : type === 'warning' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS for notification animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification {
        animation: slideInRight 0.3s ease;
    }
    
    .btn-close-white {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        margin-left: 1rem;
    }
    
    .btn-close-white:hover {
        opacity: 0.7;
    }
`;
document.head.appendChild(style);

// Server status update simulation
function updateServerStatus() {
    const statusCards = document.querySelectorAll('.status-card');
    
    statusCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('status-updated');
            setTimeout(() => {
                card.classList.remove('status-updated');
            }, 1000);
        });
    });
}

// Initialize server status updates
document.addEventListener('DOMContentLoaded', updateServerStatus);

// Smooth scroll behavior for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add floating animation to elements
function addFloatingAnimation() {
    const floatingElements = document.querySelectorAll('.about-icon, .status-icon');
    
    floatingElements.forEach((element, index) => {
        element.style.animation = `float 3s ease-in-out infinite`;
        element.style.animationDelay = `${index * 0.2}s`;
    });
}

// Initialize floating animations
document.addEventListener('DOMContentLoaded', addFloatingAnimation);

// Add glow effect on hover for interactive elements
document.addEventListener('DOMContentLoaded', function() {
    const glowElements = document.querySelectorAll('.hero-btn, .join-btn, .btn-primary');
    
    glowElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.filter = 'drop-shadow(0 0 20px rgba(47, 0, 255, 0.5))';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.filter = 'none';
        });
    });
});
