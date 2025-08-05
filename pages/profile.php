<?php session_start(); 
include '../components/navbar.php';
$skillsOptions = [
    'Communication',
    'Teamwork',
    'Leadership',
    'Programming',
    'Design',
    'Marketing',
    'Management',
];
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <link rel="stylesheet" href="/css/global.css">

</head>
<body>
    <div class="centered-page scrollable-container">
        <div class="event-container">
            <form class="form-container" action="/backend/auth/process_profile.php" method="post">
                <div class="form-box">
                    <h2 class="form-header">Profile management</h2>
                    <div class="form-group">
                        <label for="full-name">Full name</label>
                        <input type="text" name="full-name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="Address1">Address 1</label>
                        <input type="text" name="Address1" required>
                        <label for="Address2">Address 2</label>
                        <input type="text" name="Address2">
                    </div>
                    
                    <div class="form-group">
                        <label for="City">City</label>
                        <input type="text" name="City" required>
                    </div>

                    <div class="form-group">
                        <label for="State">State</label>
                        <select name="State" required>
                            <option value="" disabled selected>Select State</option>
                            <?php
                                $states = array(
                                    'AL'=>'AL', 'AK'=>'AK', 'AZ'=>'AZ', 'AR'=>'AR',
                                    'CA'=>'CA', 'CO'=>'CO', 'CT'=>'CT', 'DE'=>'DE',
                                    'FL'=>'FL', 'GA'=>'GA', 'HI'=>'HI', 'ID'=>'ID',
                                    'IL'=>'IL', 'IN'=>'IN', 'IA'=>'IA', 'KS'=>'KS',
                                    'KY'=>'KY', 'LA'=>'LA', 'ME'=>'ME', 'MD'=>'MD',
                                    'MA'=>'MA', 'MI'=>'MI', 'MN'=>'MN', 'MS'=>'MS',
                                    'MO'=>'MO', 'MT'=>'MT', 'NE'=>'NE', 'NV'=>'NV',
                                    'NH'=>'NH', 'NJ'=>'NJ', 'NM'=>'NM', 'NY'=>'NY',
                                    'NC'=>'NC', 'ND'=>'ND', 'OH'=>'OH', 'OK'=>'OK',
                                    'OR'=>'OR', 'PA'=>'PA', 'RI'=>'RI',
                                    'SC'=>'SC', 'SD'=>'SD', 'TN'=>'TN',
                                    'TX'=>'TX', 'UT'=>'UT', 'VT'=>'VT', 'VA'=>'VA',
                                    'WA'=>'WA', 'WV'=>'WV', 'WI'=>'WI', 'WY'=>'WY'
                                );
                                foreach($states as $abbr => $name) {
                                    echo "<option value=\"$abbr\">$name</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="zipcode">Zip code</label>
                        <input type="text" name="zipcode" pattern="[0-9]{5}"   required>
                    </div>

                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <select name="skills[]" multiple required>
                            <?php foreach ($skillsOptions as $skill): ?>
                                <option value="<?php echo htmlspecialchars($skill); ?>"><?php echo htmlspecialchars($skill); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="Preferences">Preferences</label>
                        <textarea name="Preferences" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="datePicker">Availability</label>
                        <input type="date" id="datePicker">
                        <button type="button" id="addBtn">Add Date</button>
                        <div id="selectedDates"></div>
                    </div>
                    <button type="submit">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    const datePicker = document.getElementById('datePicker');
    const addBtn = document.getElementById('addBtn');
    const container = document.getElementById('selectedDates');

    addBtn.addEventListener('click', () =>{
        const date = datePicker.value;
        if (!date){
            alert('Please select a date');
            return;
        }
        if ([...container.querySelectorAll('input[name="dates[]"]')].some(i => i.value === date)){
            alert('This date is already in the list');
            return;
        }
        const div = document.createElement('div');
        div.className = 'date-item';

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'dates[]';
        hidden.value = date;

        const label = document.createTextNode(date);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.textContent = 'Remove';
        remove.addEventListener('click', () => div.remove());

        div.appendChild(hidden);
        div.appendChild(label);
        div.appendChild(remove);
        container.appendChild(div);

        datePicker.value = '';
    });
</script>