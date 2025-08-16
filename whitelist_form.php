<?php
// Check if user is logged in
if (!isset($_SESSION['discord_user'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['discord_user'];
?>

<div class="whitelist-form">
    <div class="user-info">
        <div class="d-flex align-items-center justify-content-center mb-3">
            <?php if (isset($user['avatar']) && $user['avatar']): ?>
                <img src="https://cdn.discordapp.com/avatars/<?php echo $user['id']; ?>/<?php echo $user['avatar']; ?>.png?size=64" 
                     alt="Avatar" class="user-avatar me-3">
            <?php else: ?>
                <div class="user-avatar-placeholder me-3">
                    <i class="fab fa-discord"></i>
                </div>
            <?php endif; ?>
            <div>
                <h4 class="mb-1">Welcome, <?php echo htmlspecialchars($user['global_name'] ?? $user['username']); ?>!</h4>
                <p class="mb-0 text-muted">Discord ID: <?php echo htmlspecialchars($user['id']); ?></p>
            </div>
        </div>
    </div>

    <form id="whitelistForm" method="POST" action="submit_whitelist.php">
        <div class="form-header text-center mb-4">
            <h3 class="form-title">Las Vegas Role Play - Whitelist Application</h3>
            <p class="form-description">Please fill out all fields honestly and accurately. Your application will be reviewed by our administration team.</p>
        </div>

        <?php
        // Get form data from session if available (for error recovery)
        $form_data = $_SESSION['form_data'] ?? [];
        ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="real_name">1. What is your real name? *</label>
                    <input type="text" class="form-control" id="real_name" name="real_name" 
                           value="<?php echo htmlspecialchars($form_data['real_name'] ?? ''); ?>" 
                           placeholder="Enter your real name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="real_age">2. What is your real age? *</label>
                    <input type="number" class="form-control" id="real_age" name="real_age" 
                           value="<?php echo htmlspecialchars($form_data['real_age'] ?? ''); ?>" 
                           min="13" max="100" placeholder="Enter your age" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="nationality">3. What is your nationality (country)? *</label>
            <select class="form-control" id="nationality" name="nationality" required>
                <option value="">Select your country</option>
                <?php
                $countries = [
                    'Morocco', 'Algeria', 'Tunisia', 'Libya', 'Egypt', 'Sudan', 'Saudi Arabia', 'UAE',
                    'Kuwait', 'Qatar', 'Bahrain', 'Oman', 'Jordan', 'Palestine', 'Lebanon', 'Syria',
                    'Iraq', 'Yemen', 'France', 'Spain', 'Germany', 'Belgium', 'Netherlands', 'Canada', 
                    'United States', 'United Kingdom', 'Italy', 'Turkey', 'Other'
                ];
                $selected_nationality = $form_data['nationality'] ?? '';
                foreach ($countries as $country) {
                    $selected = ($country === $selected_nationality) ? 'selected' : '';
                    echo "<option value=\"{$country}\" {$selected}>{$country}</option>";
                }
                ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="character_name">4. What is your character's name in the game? *</label>
                    <input type="text" class="form-control" id="character_name" name="character_name" 
                           value="<?php echo htmlspecialchars($form_data['character_name'] ?? ''); ?>" 
                           placeholder="Example: John_Smith" required>
                    <small class="form-text">The name must be realistic and suitable for roleplay</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="character_age">5. What is your character's age in the game? *</label>
                    <input type="number" class="form-control" id="character_age" name="character_age" 
                           value="<?php echo htmlspecialchars($form_data['character_age'] ?? ''); ?>" 
                           min="18" max="80" placeholder="Character age" required>
                    <small class="form-text">Character must be 18 years or older</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="character_type">6. Legal or Illegal character? *</label>
                    <select class="form-control" id="character_type" name="character_type" required>
                        <option value="">Choose character type</option>
                        <?php
                        $character_types = ['Legal' => 'Legal (Law-abiding citizen)', 'Illegal' => 'Illegal (Criminal)'];
                        $selected_type = $form_data['character_type'] ?? '';
                        foreach ($character_types as $value => $label) {
                            $selected = ($value === $selected_type) ? 'selected' : '';
                            echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="rp_experience">7. Do you have roleplay experience? *</label>
                    <select class="form-control" id="rp_experience" name="rp_experience" required>
                        <option value="">Choose your experience level</option>
                        <?php
                        $experience_levels = [
                            'Beginner' => 'Beginner (No previous experience)',
                            'Intermediate' => 'Intermediate (Some experience)',
                            'Advanced' => 'Advanced (Good experience)',
                            'Expert' => 'Expert (Extensive experience)'
                        ];
                        $selected_experience = $form_data['rp_experience'] ?? '';
                        foreach ($experience_levels as $value => $label) {
                            $selected = ($value === $selected_experience) ? 'selected' : '';
                            echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="character_story">8. Write your character's backstory *</label>
            <textarea class="form-control" id="character_story" name="character_story" rows="8" required 
                      placeholder="Write a detailed story about your character (must be more than 5 lines)...

Example:
My name is John Smith, born in New York City in 1995. I grew up in a middle-class family, my father worked in construction and my mother was a nurse. Since childhood, I dreamed of making it big in Las Vegas.

After finishing my college degree in Business Administration, I decided to move to Las Vegas to pursue better opportunities. I arrived in the city with little money but big dreams.

Now I'm looking for honest work to start my new life in this city. I'm interested in working in business or services, and I dream of opening my own company someday.

I have a calm and respectful personality, I like helping others and believe in honest work. I don't like violence or trouble, and I prefer to resolve conflicts through dialogue."><?php echo htmlspecialchars($form_data['character_story'] ?? ''); ?></textarea>
            <small class="form-text">The story must be more than 5 lines and contain realistic details about your character</small>
            <div id="story-counter" class="character-counter">0 characters</div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    I agree to the server rules and commit to following them *
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="truthful" name="truthful" required>
                <label class="form-check-label" for="truthful">
                    I certify that all information provided is true and accurate *
                </label>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary submit-btn" id="submitBtn">
                <span class="btn-text">
                    <i class="fas fa-paper-plane me-2"></i>
                    Submit Whitelist Application
                </span>
                <span class="loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Processing...
                </span>
            </button>
        </div>
    </form>

    <div class="logout-section text-center mt-4">
        <a href="logout.php" class="btn btn-outline-danger logout-btn">
            <i class="fas fa-sign-out-alt me-2"></i>
            Logout
        </a>
    </div>
</div>

<style>
.user-info {
    background: rgba(0, 0, 0, 0.6);
    border: 1px solid rgba(0, 47, 255, 0.2);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.user-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: 3px solid var(--primary-color);
}

.user-avatar-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.form-header {
    margin-bottom: 2rem;
}

.form-title {
    color: var(--primary-color);
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 0 10px rgba(47, 0, 255, 0.3);
}

.form-description {
    background: rgba(47, 0, 255, 0.1);
    border: 1px solid rgba(47, 0, 255, 0.2);
    border-radius: 10px;
    padding: 1rem;
    color: #e0e0e0;
    margin: 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

.form-text {
    color: #aaa;
    font-size: 0.9rem;
    margin-top: 0.5rem;
    display: block;
}

.character-counter {
    text-align: right;
    font-size: 0.9rem;
    color: #aaa;
    margin-top: 0.5rem;
}

.form-check {
    padding: 1rem;
    background: rgba(47, 0, 255, 0.1);
    border: 1px solid rgba(47, 0, 255, 0.2);
    border-radius: 10px;
    margin-bottom: 1rem;
}

.form-check-input {
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(47, 0, 255, 0.2);
    margin-right: 0.5rem;
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-check-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(47, 0, 255, 0.25);
}

.form-check-label {
    color: #e0e0e0;
    font-weight: 500;
}

.submit-btn {
    padding: 1rem 3rem;
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 50px;
    min-width: 250px;
    position: relative;
    overflow: hidden;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
}

.submit-btn:hover::before {
    left: 100%;
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-text {
    display: inline;
}

.submit-btn:disabled .btn-text {
    display: none;
}

.submit-btn:disabled .loading {
    display: inline-block !important;
}

.logout-section {
    padding-top: 2rem;
    border-top: 1px solid rgba(47, 0, 255, 0.2);
}

.logout-btn {
    border-color: #dc3545;
    color: #dc3545;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .user-info .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .user-avatar, .user-avatar-placeholder {
        margin-bottom: 1rem;
    }
    
    .form-title {
        font-size: 1.5rem;
    }
    
    .submit-btn {
        min-width: 200px;
        padding: 0.75rem 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for story textarea
    const storyTextarea = document.getElementById('character_story');
    const storyCounter = document.getElementById('story-counter');
    
    if (storyTextarea && storyCounter) {
        function updateCounter() {
            const count = storyTextarea.value.length;
            storyCounter.textContent = count + ' characters';
            
            if (count < 500) {
                storyCounter.style.color = '#dc3545';
            } else if (count < 1000) {
                storyCounter.style.color = '#ffc107';
            } else {
                storyCounter.style.color = '#28a745';
            }
        }
        
        storyTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial count
    }
    
    // Form submission handling
    const form = document.getElementById('whitelistForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            
            // Basic validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            // Check story length
            const story = document.getElementById('character_story');
            if (story && story.value.length < 500) {
                isValid = false;
                story.style.borderColor = '#dc3545';
                alert('Character story must be at least 500 characters long.');
            }
            
            if (!isValid) {
                e.preventDefault();
                submitBtn.disabled = false;
                return false;
            }
        });
    }
    
    // Add floating animation to form elements
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        group.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        setTimeout(() => {
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
