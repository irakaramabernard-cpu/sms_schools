<?php
// This file contains the SQL commands to set up the complete grade/class system
// Run these in phpMyAdmin

$sql_commands = "

-- Add grade/class column to students table if not exists
ALTER TABLE students ADD COLUMN IF NOT EXISTS grade VARCHAR(50);
ALTER TABLE students ADD COLUMN IF NOT EXISTS class VARCHAR(50);
ALTER TABLE students ADD COLUMN IF NOT EXISTS marks INT DEFAULT 0;

-- Create grades table
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    age_range VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grade_id) REFERENCES grades(id) ON DELETE CASCADE
);

-- Insert grade levels
INSERT INTO grades (grade_name, description, age_range) VALUES
('Lower Primary Grade 1', 'Lower Primary - Grade 1', '6-7 years'),
('Lower Primary Grade 2', 'Lower Primary - Grade 2', '7-8 years'),
('Lower Primary Grade 3', 'Lower Primary - Grade 3', '8-9 years'),
('Upper Primary Grade 4', 'Upper Primary - Grade 4', '9-10 years'),
('Upper Primary Grade 5', 'Upper Primary - Grade 5', '10-11 years'),
('Upper Primary Grade 6', 'Upper Primary - Grade 6', '11-12 years'),
('O-Level Grade 7', 'Secondary O-Level - Grade 7', '12-13 years'),
('O-Level Grade 8', 'Secondary O-Level - Grade 8', '13-14 years'),
('O-Level Grade 9', 'Secondary O-Level - Grade 9', '14-15 years'),
('O-Level Grade 10', 'Secondary O-Level - Grade 10', '15-16 years'),
('O-Level Grade 11', 'Secondary O-Level - Grade 11', '16-17 years'),
('A-Level Grade 12', 'Secondary A-Level - Grade 12', '17-18 years'),
('A-Level Grade 13', 'Secondary A-Level - Grade 13', '18-19 years')
ON DUPLICATE KEY UPDATE description=VALUES(description);

";

echo nl2br($sql_commands);
?>
