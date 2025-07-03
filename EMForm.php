<?php
$skillsOptions = [
    'Communication',
    'Teamwork',
    'Leadership',
    'Programming',
    'Design',
    'Marketing',
    'Management',
];

$urgencyLevels = [
    'Low',
    'Medium',
    'High',
    'Critical',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="/css/global.css">
</head>
<body>
    <h1>Create New Event</h1>
    <form action="submit_event.php" method="post">
        <div class="form-group">
            <label for="event_name">Event Name <span>*</span></label><br>
            <input type="text" id="event_name" name="event_name" maxlength="100" required placeholder="Enter event name">
        </div>

        <div class="form-group">
            <label for="event_description">Event Description <span>*</span></label><br>
            <textarea id="event_description" name="event_description" rows="4" required placeholder="Describe the event"></textarea>
        </div>

        <div class="form-group">
            <label for="location">Location <span>*</span></label><br>
            <textarea id="location" name="location" rows="2" required placeholder="Enter the venue or address"></textarea>
        </div>

        <div class="form-group">
            <label for="required_skills">Required Skills <span>*</span></label><br>
            <select id="required_skills" name="required_skills[]" multiple size="<?php echo min(5, count($skillsOptions)); ?>" required>
                <?php foreach ($skillsOptions as $skill): ?>
                    <option value="<?php echo htmlspecialchars($skill); ?>"><?php echo htmlspecialchars($skill); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="urgency">Urgency <span>*</span></label><br>
            <select id="urgency" name="urgency" required>
                <option value="" disabled selected>Select urgency</option>
                <?php foreach ($urgencyLevels as $level): ?>
                    <option value="<?php echo strtolower($level); ?>"><?php echo $level; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="event_date">Event Date <span>*</span></label><br>
            <input type="date" id="event_date" name="event_date" required>
        </div>

        <button type="submit">Create Event</button>
    </form>
</body>
</html>
