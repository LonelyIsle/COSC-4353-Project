document.addEventListener('DOMContentLoaded', () => {
  const pageNotificationBtn = document.getElementById('pageNotificationBtn');
  const dropdown = document.getElementById('notificationDropdown');
  const notificationList = document.getElementById('notificationList');
  const notificationBadge = document.getElementById('notificationBadge');

  async function fetchAndRenderNotifications() {
    notificationList.innerHTML = '';
    notificationBadge.style.display = 'none';
    try {
      const response = await fetch('/backend/auth/get_notifications.php');
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const notifications = await response.json();

      if (notifications.length > 0) {
        notificationBadge.textContent = notifications.length;
        notificationBadge.style.display = 'inline-block';
        notifications.forEach(note => {
          const li = document.createElement('li');
          li.textContent = note;
          notificationList.appendChild(li);
        });
      } else {
        notificationList.innerHTML = '<li class="no-notifications">No new notifications</li>';
      }
    } catch (error) {
      console.error('Error fetching notifications:', error);
      notificationList.innerHTML = '<li class="no-notifications">Error loading notifications.</li>';
    }
  }

  async function markNotificationsAsRead() {
    try {
      await fetch('/backend/auth/mark_notifcations.php', { method: 'POST' });
      fetchAndRenderNotifications();
    } catch (error) {
      console.error('Error marking notifications as read:', error);
    }
  }

  if (pageNotificationBtn) {
    pageNotificationBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
      if (
        dropdown.style.display === 'block' &&
        !pageNotificationBtn.contains(e.target) &&
        !dropdown.contains(e.target)
      ) {
        if (notificationBadge.style.display === 'inline-block') {
          markNotificationsAsRead();
        }
        dropdown.style.display = 'none';
      }
    });

    fetchAndRenderNotifications();
    setInterval(fetchAndRenderNotifications, 5000);
  }
});