<?php
session_start();
require_once 'includes/db_connect.php'; // Adjust the path as needed

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: includes/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Dalhousie Forum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css"> <!-- Custom CSS -->
    <script src="assets/scripts.js" type="module" defer></script>
</head>
<body>
    <?php include 'templates/header.php'; ?>
    <!-- Three.js Background -->
    <div class="relative">
        <div class="grid-icosahedron"></div>
    </div>
</div>
    <div class="container-fluid d-flex flex-column align-items-center mt-5">
        <div class="message-container">
            <h1 class="message-heading">Messages</h1>
            <button class="toggle-btn" onclick="toggleMessageForm()">New Message</button>

            <!-- New Message Form -->
            <div id="message-form" class="message-form" style="display: none;">
                <form id="new-message-form">
                    <div class="form-group">
                        <label for="recipient">Recipient Username:</label>
                        <input type="text" id="recipient" class="form-control" placeholder="Enter recipient username" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <i class="fas fa-paper-plane"></i>
                        <textarea id="message" class="form-control" rows="4" placeholder="Write your message..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                    
                </form>
            </div>

            <!-- Received Messages Table -->
            <h2>Received Messages</h2>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="received-messages"></tbody>
                </table>
            </div>

            <!-- Sent Messages Table -->
            <h2>Sent Messages</h2>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>To</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="sent-messages"></tbody>
                </table>
            </div>
            <button class="btn" type="submit">
        <a href="index.php">
                <span>Go Back to Forum Page</span>
                <i class="fas fa-arrow-right"></i>
                </a>
            </button>
        </div>
        
    </div>

    <script>
        const user_id = <?= $_SESSION['user_id']; ?>;
        const receivedMessagesTable = document.getElementById('received-messages');
        const sentMessagesTable = document.getElementById('sent-messages');
        const messageForm = document.getElementById('message-form');
        const newMessageForm = document.getElementById('new-message-form');

        function toggleMessageForm() {
            messageForm.style.display = messageForm.style.display === 'none' ? 'block' : 'none';
        }

        async function fetchMessages() {
            try {
                const response = await fetch('api/messages.php');
                if (!response.ok) throw new Error('Error fetching messages');

                const messages = await response.json();
                if (messages.error) throw new Error(messages.error);

                // Clear existing rows
                receivedMessagesTable.innerHTML = '';
                sentMessagesTable.innerHTML = '';

                messages.forEach((message) => {
                    const row = document.createElement('tr');

                    if (message.receiver_id === user_id) {
                        row.innerHTML = `
                            <td>${message.sender_name}</td>
                            <td>${message.content}</td>
                            <td>${new Date(message.timestamp).toLocaleString()}</td>
                        `;
                        receivedMessagesTable.appendChild(row);
                    } else if (message.sender_id === user_id) {
                        row.innerHTML = `
                            <td>${message.receiver_name}</td>
                            <td>${message.content}</td>
                            <td>${new Date(message.timestamp).toLocaleString()}</td>
                        `;
                        sentMessagesTable.appendChild(row);
                    }
                });
            } catch (error) {
                console.error('Error:', error.message);
                alert('Error fetching messages.');
            }
        }

        newMessageForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const recipient = document.getElementById('recipient').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!recipient || !message) {
                alert('Please fill out both fields.');
                return;
            }

            try {
                const response = await fetch('api/messages.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ recipient, message }),
                });

                const result = await response.json();
                if (result.success) {
                    alert('Message sent successfully!');
                    toggleMessageForm();
                    fetchMessages();
                } else {
                    alert(result.error);
                }
            } catch (error) {
                console.error('Error sending message:', error.message);
            }
        });

        setInterval(fetchMessages, 5000);
        fetchMessages();
    </script>
       <?php include 'templates/footer.php'; ?>
</body>
</html>