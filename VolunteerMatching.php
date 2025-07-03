<?php

$volunteers = [
    'Alice Smith',
    'Bob Johnson',
    'Carol Lee',
];
$matchedEvents = [
    'Community Cleanup',
    'Food Drive',
    'Food Distribution',
    'Shelter Kitchen',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Volunteer Matching</title>
  <link rel="stylesheet" href="/css/global.css">
</head>
<body>
  <div class="centered-page">
    <div class="event-container">
      <h2>Volunteer Matching</h2>
      <form action="process_match.php" method="post">
        
        <div class="form-group">
          <label for="volunteer_name">Volunteer Name</label><br>
          <select id="volunteer_name" name="volunteer_name" required>
            <option value="" disabled selected>Select a volunteer</option>
            <?php foreach ($volunteers as $v): ?>
              <option value="<?php echo htmlspecialchars($v) ?>">
                <?php echo htmlspecialchars($v) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="matched_event">Matched Event</label><br>
          <select id="matched_event" name="matched_event" required>
            <option value="" disabled selected>Select matched event</option>
            <?php foreach ($matchedEvents as $e): ?>
              <option value="<?php echo htmlspecialchars($e) ?>">
                <?php echo htmlspecialchars($e) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit">Match Volunteer</button>
      </form>
    </div>
  </div>
</body>
</html>
