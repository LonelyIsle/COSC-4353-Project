<?php session_start(); 
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
    <link rel="stylesheet" href="css/global.css">

</head>
<body>
    <div class="centered-page">
        <div class="event-container">
            <form class="form-container" action="backend/auth/process_profile.php" method="post">
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
                                    'AL'=>'Alabama', 'AK'=>'Alaska', 'AZ'=>'Arizona', 'AR'=>'Arkansas',
                                    'CA'=>'California', 'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware',
                                    'FL'=>'Florida', 'GA'=>'Georgia', 'HI'=>'Hawaii', 'ID'=>'Idaho',
                                    'IL'=>'Illinois', 'IN'=>'Indiana', 'IA'=>'Iowa', 'KS'=>'Kansas',
                                    'KY'=>'Kentucky', 'LA'=>'Louisiana', 'ME'=>'Maine', 'MD'=>'Maryland',
                                    'MA'=>'Massachusetts', 'MI'=>'Michigan', 'MN'=>'Minnesota', 'MS'=>'Mississippi',
                                    'MO'=>'Missouri', 'MT'=>'Montana', 'NE'=>'Nebraska', 'NV'=>'Nevada',
                                    'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NY'=>'New York',
                                    'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio', 'OK'=>'Oklahoma',
                                    'OR'=>'Oregon', 'PA'=>'Pennsylvania', 'RI'=>'Rhode Island',
                                    'SC'=>'South Carolina', 'SD'=>'South Dakota', 'TN'=>'Tennessee',
                                    'TX'=>'Texas', 'UT'=>'Utah', 'VT'=>'Vermont', 'VA'=>'Virginia',
                                    'WA'=>'Washington', 'WV'=>'West Virginia', 'WI'=>'Wisconsin', 'WY'=>'Wyoming'
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
                        <input type="date" id="datePicker" required>
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