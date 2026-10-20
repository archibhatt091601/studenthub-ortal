CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login DATETIME NULL
);

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  mobile VARCHAR(15) NULL,
  course VARCHAR(100) NOT NULL,
  year VARCHAR(50) NOT NULL,
  gender ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  event_date DATE NOT NULL,
  venue VARCHAR(150) DEFAULT 'Campus',
  category VARCHAR(50) DEFAULT 'Workshop',
  poster_image VARCHAR(255) DEFAULT NULL,
  status ENUM('upcoming', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  event_id INT NOT NULL,
  registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('confirmed', 'pending', 'cancelled') NOT NULL DEFAULT 'confirmed',
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_email VARCHAR(100) NOT NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@studenthub.com', '$2y$10$m4yXvUPPuqgkwrRhzOaHpuAJ9ont0yQ6jLckeceTe0lcEaaOz9D8q', 'admin'),
('Student User', 'student@studenthub.com', '$2y$10$LgMnBB2DNAxKBBNaMiJ3aujdDf.E8TzaUiitsMIJ8DOZtT2VAY2VS', 'student')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO students (id, name, email, mobile, course, year, gender, status) VALUES
(1, 'Vatsal Makwana', 'vatsal@gmail.com', '9825011111', 'Computer Engineering', 'Second Year', 'Male', 'active'),
(2, 'Rahul Sharma', 'rahul@gmail.com', '9825022222', 'Computer Engineering', 'Second Year', 'Male', 'active'),
(3, 'Raj Patel', 'raj@gmail.com', '9825033333', 'Information Technology', 'Third Year', 'Male', 'active'),
(4, 'Diya Shah', 'diya@gmail.com', '9825044444', 'Information Technology', 'First Year', 'Female', 'active'),
(5, 'Ananya Joshi', 'ananya@gmail.com', '9825055555', 'Civil Engineering', 'Second Year', 'Female', 'active'),
(6, 'Karan Desai', 'karan@gmail.com', '9825066666', 'Mechanical Engineering', 'Fourth Year', 'Male', 'active'),
(7, 'Pooja Trivedi', 'pooja@gmail.com', '9825077777', 'Information Technology', 'Third Year', 'Female', 'active'),
(8, 'Varun Dave', 'varun@gmail.com', '9825088888', 'Computer Engineering', 'First Year', 'Male', 'active'),
(9, 'Neha Parmar', 'neha@gmail.com', '9825099999', 'Civil Engineering', 'Third Year', 'Female', 'active'),
(10, 'Harsh Vora', 'harsh@gmail.com', '9825012345', 'Mechanical Engineering', 'Second Year', 'Male', 'active'),
(11, 'Riddhi Soni', 'riddhi@gmail.com', '9825023456', 'Information Technology', 'Fourth Year', 'Female', 'active'),
(12, 'Yash Bhatt', 'yash@gmail.com', '9825034567', 'Computer Engineering', 'Second Year', 'Male', 'active'),
(13, 'Isha Modi', 'isha@gmail.com', '9825045678', 'Information Technology', 'First Year', 'Female', 'active'),
(14, 'Manan Pandya', 'manan@gmail.com', '9825056789', 'Civil Engineering', 'Fourth Year', 'Male', 'active'),
(15, 'Kinjal Panchal', 'kinjal@gmail.com', '9825067890', 'Mechanical Engineering', 'Third Year', 'Female', 'active'),
(16, 'Dhruv Raval', 'dhruv@gmail.com', '9825078901', 'Computer Engineering', 'Third Year', 'Male', 'active'),
(17, 'Tanvi Vyas', 'tanvi@gmail.com', '9825089012', 'Information Technology', 'Second Year', 'Female', 'active'),
(18, 'Siddharth Solanki', 'siddharth@gmail.com', '9825090123', 'Computer Engineering', 'Fourth Year', 'Male', 'active'),
(19, 'Meera Barot', 'meera@gmail.com', '9825001234', 'Civil Engineering', 'First Year', 'Female', 'active'),
(20, 'Pranav Shah', 'pranav@gmail.com', '9825010203', 'Mechanical Engineering', 'Second Year', 'Male', 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO events (id, title, event_date, description, venue, category, status) VALUES
(1, 'Coding Competition', '2026-10-20', 'Programming competition for students', 'Lab 1', 'Competition', 'completed'),
(2, 'Web Development Workshop', '2026-10-25', 'Workshop on modern web development', 'Seminar Hall', 'Workshop', 'upcoming'),
(3, 'Tech Fest', '2026-11-05', 'Annual technical festival', 'Auditorium', 'Festival', 'upcoming'),
(4, 'AI & Robotics Seminar', '2026-11-15', 'Seminar on machine learning and robotics', 'Hall 2', 'Seminar', 'upcoming'),
(5, 'Campus Sports Meet', '2026-11-20', 'Inter-department athletic events', 'College Ground', 'Sports', 'upcoming')
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO registrations (id, student_id, event_id, status) VALUES
(1, 1, 1, 'confirmed'),
(2, 2, 1, 'confirmed'),
(3, 1, 2, 'confirmed'),
(4, 3, 3, 'confirmed'),
(5, 4, 2, 'confirmed')
ON DUPLICATE KEY UPDATE status=VALUES(status);
