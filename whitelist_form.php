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
        <h3>مرحباً، <?php echo htmlspecialchars($user['global_name']); ?>!</h3>
        <p>Discord ID: <?php echo htmlspecialchars($user['id']); ?></p>
        <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
    </div>

    <form id="whitelistForm" method="POST" action="submit_whitelist.php">
        <h3>طلب الانضمام إلى سيرفر لاس فيغاس</h3>
        <p class="form-description">يرجى ملء جميع الحقول بصدق ودقة. سيتم مراجعة طلبك من قبل الإدارة.</p>

        <?php
        // Get form data from session if available (for error recovery)
        $form_data = $_SESSION['form_data'] ?? [];
        ?>

        <div class="form-group">
            <label for="real_name">1. ما اسمك الحقيقي؟ *</label>
            <input type="text" id="real_name" name="real_name" value="<?php echo htmlspecialchars($form_data['real_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="real_age">2. ما هو عمرك الحقيقي؟ *</label>
            <input type="number" id="real_age" name="real_age" value="<?php echo htmlspecialchars($form_data['real_age'] ?? ''); ?>" min="13" max="100" required>
        </div>

        <div class="form-group">
            <label for="nationality">3. ماهي جنسيتك (ماهو بلدك)؟ *</label>
            <select id="nationality" name="nationality" required>
                <option value="">اختر بلدك</option>
                <?php
                $countries = [
                    'المغرب', 'الجزائر', 'تونس', 'ليبيا', 'مصر', 'السودان', 'السعودية', 'الإمارات',
                    'الكويت', 'قطر', 'البحرين', 'عمان', 'الأردن', 'فلسطين', 'لبنان', 'سوريا',
                    'العراق', 'اليمن', 'فرنسا', 'إسبانيا', 'ألمانيا', 'بلجيكا', 'هولندا', 'كندا', 'أمريكا', 'أخرى'
                ];
                $selected_nationality = $form_data['nationality'] ?? '';
                foreach ($countries as $country) {
                    $selected = ($country === $selected_nationality) ? 'selected' : '';
                    echo "<option value=\"{$country}\" {$selected}>{$country}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="character_name">4. ما اسم شخصيتك في اللعبة؟ *</label>
            <input type="text" id="character_name" name="character_name" value="<?php echo htmlspecialchars($form_data['character_name'] ?? ''); ?>" placeholder="مثال: Ahmed_Hassan" required>
            <small>يجب أن يكون الاسم واقعياً ومناسباً للرول بلاي</small>
        </div>

        <div class="form-group">
            <label for="character_age">5. ما عمر الشخصية في اللعبة؟ *</label>
            <input type="number" id="character_age" name="character_age" value="<?php echo htmlspecialchars($form_data['character_age'] ?? ''); ?>" min="18" max="80" required>
            <small>يجب أن يكون عمر الشخصية 18 سنة أو أكثر</small>
        </div>

        <div class="form-group">
            <label for="character_type">6. قانوني أو غير قانوني؟ *</label>
            <select id="character_type" name="character_type" required>
                <option value="">اختر نوع الشخصية</option>
                <?php
                $character_types = ['قانوني' => 'قانوني (Legal)', 'غير قانوني' => 'غير قانوني (Illegal)'];
                $selected_type = $form_data['character_type'] ?? '';
                foreach ($character_types as $value => $label) {
                    $selected = ($value === $selected_type) ? 'selected' : '';
                    echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="rp_experience">7. هل عندك خبرة في الرول بلاي؟ *</label>
            <select id="rp_experience" name="rp_experience" required>
                <option value="">اختر مستوى خبرتك</option>
                <?php
                $experience_levels = [
                    'مبتدئ' => 'مبتدئ (لا توجد خبرة سابقة)',
                    'متوسط' => 'متوسط (خبرة قليلة)',
                    'متقدم' => 'متقدم (خبرة جيدة)',
                    'خبير' => 'خبير (خبرة كبيرة)'
                ];
                $selected_experience = $form_data['rp_experience'] ?? '';
                foreach ($experience_levels as $value => $label) {
                    $selected = ($value === $selected_experience) ? 'selected' : '';
                    echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="character_story">8. اكتب قصة شخصيتك *</label>
            <textarea id="character_story" name="character_story" rows="8" required 
                      placeholder="اكتب قصة مفصلة عن شخصيتك (يجب أن تكون أكثر من 5 أسطر)...

مثال:
اسمي أحمد حسن، ولدت في مدينة الدار البيضاء بالمغرب عام 1995. نشأت في عائلة متوسطة الحال، والدي يعمل في التجارة ووالدتي معلمة. منذ صغري كنت أحلم بالسفر إلى أمريكا لتحقيق أحلامي.

بعد إنهاء دراستي الجامعية في إدارة الأعمال، قررت السفر إلى لاس فيغاس للبحث عن فرص عمل أفضل. وصلت إلى المدينة بمبلغ قليل من المال وأحلام كبيرة.

الآن أبحث عن عمل شريف لأبدأ حياتي الجديدة في هذه المدينة. أتطلع للعمل في مجال التجارة أو الخدمات، وأحلم بأن أفتح مشروعي الخاص يوماً ما.

شخصيتي هادئة ومحترمة، أحب مساعدة الآخرين وأؤمن بالعمل الشريف. لا أحب العنف أو المشاكل، وأفضل حل النزاعات بالحوار."><?php echo htmlspecialchars($form_data['character_story'] ?? ''); ?></textarea>
            <small>يجب أن تكون القصة أكثر من 5 أسطر وتحتوي على تفاصيل واقعية عن شخصيتك</small>
            <div id="story-counter" class="character-counter">0 حرف</div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" id="terms" name="terms" required>
                أوافق على قوانين السيرفر وأتعهد بالالتزام بها *
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" id="truthful" name="truthful" required>
                أتعهد بأن جميع المعلومات المقدمة صحيحة وصادقة *
            </label>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
            <span class="btn-text">إرسال طلب الانضمام</span>
            <span class="loading" style="display: none;"></span>
        </button>
    </form>

    <div class="logout-section">
        <a href="logout.php" class="logout-btn">تسجيل الخروج</a>
    </div>
</div>

<style>
.user-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}

.user-info h3 {
    color: #667eea;
    margin-bottom: 10px;
}

.user-info p {
    margin: 5px 0;
    color: #666;
}

.form-description {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 30px;
    color: #1565c0;
    text-align: center;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.9rem;
}

.character-counter {
    text-align: left;
    font-size: 0.9rem;
    color: #666;
    margin-top: 5px;
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-left: 10px;
}

.logout-section {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.logout-btn {
    color: #dc3545;
    text-decoration: none;
    font-weight: 500;
    padding: 10px 20px;
    border: 1px solid #dc3545;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background: #dc3545;
    color: white;
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
</style>
