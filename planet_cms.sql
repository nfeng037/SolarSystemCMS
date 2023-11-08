-- 创建用户表
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  user_type ENUM('admin', 'visitor') NOT NULL
);

-- 创建评论表
CREATE TABLE comments (
  comment_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  celestial_body_id INT NOT NULL,
  content TEXT NOT NULL,
  creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (celestial_body_id) REFERENCES celestial_bodies(celestial_body_id)
);

-- 创建分类表
CREATE TABLE categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name ENUM('Terrestrial Planets', 'Gas Giants', 'Ice Giants', 'Dwarf Planets', 'Exoplanets') NOT NULL
);

-- 创建天体表
CREATE TABLE celestial_bodies (
  celestial_body_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category_id INT,
  description TEXT,
  discovery_date DATE,
  image_url VARCHAR(255),
  FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- 创建页面表
CREATE TABLE pages (
  page_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  creator_id INT NOT NULL,
  creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_modified_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(user_id)
);
