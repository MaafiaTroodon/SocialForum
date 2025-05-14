# CSCI 2170: Intro to Server-Side Scripting

__*Assignment 4 - Dalhousie University Forum*__

## Student Information

- __Name__: Malhar Datta Mahajan
- __Student ID__: B00934337
- __Date Created__: 4 December 2024

## Overview

The Dalhousie Forum application is a server-side scripting project designed as an interactive platform for students and faculty to share their thoughts, ideas, and questions. Developed using PHP, MySQL, JavaScript, and CSS, the application allows users to create, edit, delete, and comment on forum posts. The front-end employs responsive design principles to ensure compatibility with devices of varying screen sizes, and Bootstrap was used to streamline UI development. A custom Three.js-powered background enhances the visual appeal, while the application adheres to best practices for secure user authentication and SQL queries.

Users can interact with the forum through login credentials. Logged-in users have full access to the system, allowing them to manage their posts and comments, while guests are restricted to viewing content. The forum feed dynamically fetches posts from a database and displays them in descending order of creation date. Moderation functionalities, such as editing or deleting posts, are only accessible to logged-in users. Notifications and hover animations enhance the interactivity of the platform.

To test the application, you can use the provided test credentials:
	•	Username: john_doe
	•	Password: password123

By logging in, they can perform end-to-end testing of all functionalities, including editing and deleting posts, and submitting comments.


## Testing & Use Cases

	•	Login System: Use the credentials provided to test the login system. Verify both successful and failed login attempts.
	•	Forum Feed: Check the dynamic population of posts and comments from the database.
	•	Create Post: Add a new post and verify its display on the forum feed.
	•	Edit Post: Use the edit functionality to modify an existing post.
	•	Delete Post: Remove a post and confirm its absence in the feed.
	•	Comment on Post: Add a comment and validate its presence in the comment section of the respective post.
	•	Guest Mode: Access the application without logging in and confirm that editing and deleting options are not visible.
	•	Responsive Design: Test the application on various screen sizes to ensure that all elements are correctly displayed.
	•	Error Handling: Test scenarios with invalid input or database errors to validate error messages.
	•	Security: Verify the implementation of prepared statements to prevent SQL injection.

## Citations

1. Logo generated using DeepAI’s logo generator tool available at https://deepai.org/machine-learning-model/logo-generator, accessed on November 23, 2024.

2.	The content for the post titled Queens vs Dalhousie is adapted from Reddit at https://www.reddit.com/r/Dalhousie/comments/1bzgpfq/queens_vs_dalhousie/, accessed on November 28, 2024.

3.	The content for the post titled Honest thoughts, as a student what do you think of Dal? is adapted from Reddit at https://www.reddit.com/r/Dalhousie/comments/105vp5h/honest_thoughts_as_a_student_what_do_you_think_of/, accessed on November 28, 2024.

4.	The content for the post titled Dal reputation in Canada is adapted from Reddit at https://www.reddit.com/r/Dalhousie/comments/113bg75/dal_reputation_in_canada/, accessed on November 28, 2024.

5. The hover effect and animation techniques for buttons are inspired by the example “Animated Buttons” on CodePen by Dicson. Available at https://codepen.io/dicson/pen/edoaaY, accessed on December 4, 2024.

6. The Three.js object implementation draws inspiration from the example “Three.js Glowing Sphere” on CodePen by Alphardex. Available at https://codepen.io/alphardex/pen/dyOQyPJ, accessed on December 4, 2024.

7. The fonts used throughout the document are sourced from Google Fonts, specifically utilizing the “Parkinsans”font families. These fonts were integrated into the project via the Google Fonts embed link available at https://fonts.google.com/selection/embed, accessed on December 4, 2024.

8.	Bootstrap Framework: The layout and responsive design are based on Bootstrap v5.3, which was integrated via its official CDN. Documentation and resources were accessed at https://getbootstrap.com/, accessed on December 4, 2024.

9.	Responsive Web Design: Responsive design principles, including @media queries and flexible layouts, were inspired by CSS-Tricks’ article “A Complete Guide to Flexbox.” Available at https://css-tricks.com/snippets/css/a-guide-to-flexbox/, accessed on December 2, 2024.

10. CSS Box Shadows: The box shadow techniques used for containers and buttons were inspired by tutorials on Smashing Magazine. Available at https://www.smashingmagazine.com/, accessed on December 3, 2024.

11. Code re-used from Assignment 3 (CSCI 2170) for implementing the login and logout functionalities. The original code was adapted to suit the specific requirements of Assignment 4. Accessed on December 4, 2024.
