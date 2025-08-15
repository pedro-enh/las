document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Character counter for story textarea
    const storyTextarea = document.getElementById('character_story');
    const storyCounter = document.getElementById('story-counter');
    
    if (storyTextarea && storyCounter) {
        function updateCounter() {
            const count = storyTextarea.value.length;
            storyCounter.textContent = count + ' حرف';
            
            // Change color based on length
            if (count < 200) {
                storyCounter.style.color = '#dc3545';
            } else if (count < 500) {
                storyCounter.style.color = '#ffc107';
            } else {
                storyCounter.style.color = '#28a745';
            }
        }
        
        storyTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial count
    }

    // Form validation
    const whitelistForm = document.getElementById('whitelistForm');
    if (whitelistForm) {
        whitelistForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            // Validate story length (at least 5 lines)
            const story = storyTextarea.value.trim();
            const lines = story.split('\n').filter(line => line.trim() !== '');
            
            if (lines.length < 5) {
                e.preventDefault();
                alert('يجب أن تكون قصة الشخصية أكثر من 5 أسطر');
                return;
            }
            
            if (story.length < 200) {
                e.preventDefault();
                alert('قصة الشخصية قصيرة جداً. يرجى كتابة قصة أكثر تفصيلاً');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            loading.style.display = 'inline-block';
        });
    }

    // Auto-hide messages after 5 seconds
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });

    // Add click effect to buttons
    const buttons = document.querySelectorAll('.cta-button, .discord-btn, .submit-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Navbar background change on scroll
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(0,0,0,0.95)';
            } else {
                navbar.style.background = 'rgba(0,0,0,0.9)';
            }
        });
    }

    // Form field validation feedback
    const formInputs = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');
    formInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        
        // Remove previous validation classes
        field.classList.remove('valid', 'invalid');
        
        if (isRequired && !value) {
            field.classList.add('invalid');
            return false;
        }
        
        // Specific validations
        if (field.type === 'number') {
            const min = parseInt(field.getAttribute('min'));
            const max = parseInt(field.getAttribute('max'));
            const numValue = parseInt(value);
            
            if (value && (numValue < min || numValue > max)) {
                field.classList.add('invalid');
                return false;
            }
        }
        
        if (field.id === 'character_name') {
            // Check for realistic name format
            const namePattern = /^[A-Za-z]+_[A-Za-z]+$/;
            if (value && !namePattern.test(value)) {
                field.classList.add('invalid');
                return false;
            }
        }
        
        if (value) {
            field.classList.add('valid');
        }
        
        return true;
    }

    // Add animation to cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe cards for animation
    const cards = document.querySelectorAll('.about-card, .rule-item');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});

// Add CSS for ripple effect and validation styles
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .form-group input.valid,
    .form-group select.valid,
    .form-group textarea.valid {
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40,167,69,0.1);
    }

    .form-group input.invalid,
    .form-group select.invalid,
    .form-group textarea.invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
    }

    .form-group {
        position: relative;
    }

    .cta-button,
    .discord-btn,
    .submit-btn {
        position: relative;
        overflow: hidden;
    }
`;

document.head.appendChild(style);
