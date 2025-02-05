-- Create the database
CREATE DATABASE user_database;

-- Use the database
USE user_database;

-- Table for users
CREATE TABLE users (
    uid INT PRIMARY KEY,
    name VARCHAR(255),
    grade INT,
    image VARCHAR(255),
    bio TEXT,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

-- Insert initial data into users table
INSERT INTO users (uid, name, grade, image, bio, username, password) VALUES
(202, 'Hassan', 12, '/images/male.jpeg', 'adgafdg', 'hasan', '1'),
(201, 'Uriah Salas', 8, '/images/male.jpeg', 'A creative tech enthusiast skilled in HTML, JavaScript, photography, and Photoshop, blending technology and art to explore web development\'s dynamic world.', 'UriahSalas12', '2324'),
(102, 'John Smith', 9, '/images/male.jpeg', 'Driven by a unique blend of interests in technology, sports, and auto, this student excels in HTML, JavaScript, and understands the mechanics behind fitness and electronics.', 'johnsmith102', 'SmithJohn456!'),
(103, 'Pat Jones', 9, '/images/Nonbinary.png', 'A tech-savvy gourmand mastering MySQL, PHP, with a flair for nutrition, baking, and capturing culinary creations through the lens of photography.', 'patjones103', 'JonesPat789!'),
(104, 'Serena', 7, '/images/female.png', 'A budding biologist with a keen interest in technology, adept in Python, data analysis, and capturing the beauty of nature through photography.', 'Serenaliang', 'liang3378'),
(105, 'Sam Green', 12, '/images/Nonbinary.png', 'An artistic soul passionate about culinary arts, combining her painting skills and baking prowess with graphic design to create visually stunning edible art.', 'samgreen', 'GreenArt12!'),
(106, 'Liam Brown', 11, '/images/male.jpeg', 'A sports enthusiast and math wizard, leveraging statistics and exercise physiology knowledge to coach peers and analyze sports performances.', 'liambrown', 'BrownStats11!'),
(107, 'Max Martinez', 10, '/images/Nonbinary.png', 'A science lover with a knack for technology, using JavaScript to model biological and chemical processes in interactive ways.', 'maxmartinez', 'MartinezTech10!'),
(108, 'Ethan Taylor', 12, '/images/male.jpeg', 'Merging interests in auto and technology, Ethan applies mechanical engineering principles and CAD skills to innovate and solve problems.', 'ethantaylor', 'TaylorTech2023!'),
(109, 'Mia Gonzalez', 9, '/images/female.png', 'A culinary artist and photographer, Mia combines her cooking talent with digital art skills to create visually appealing and delicious dishes.', 'miagonzalez', 'GonzalezArt9!');
