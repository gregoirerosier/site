<?php
declare(strict_types=1);
$academyConfig=[
 'slug'=>'coding-school','title'=>'Beyond Coding School','icon'=>'💻','accent'=>'#6d4aff','base'=>'/coding-school/','css'=>'/coding-school/academy.css','headline'=>'Learn to think, build, and code.','description'=>'Five age-based coding modules with 10 lessons, a test after every lesson, and a final exam in every module. Module 1 is free for each age group.',
 'tracks'=>[
  'preschool'=>['Sequences & Patterns','Commands & Directions','Loops Through Play','Events & Choices','Creative Coding Stories'],
  'kids'=>['Coding Foundations','Block Coding & Animation','Web Page Basics','Python Starters','Games & Creative Projects'],
  'preteen'=>['Web Foundations','JavaScript Interactions','Python Programming','Data & Simple APIs','Projects, Git & Online Safety'],
  'teen'=>['Frontend Development','JavaScript Applications','Python Automation','Databases & APIs','Full-Stack Portfolio'],
  'adult'=>['Digital & Coding Foundations','Practical Web Development','Python & Automation','SQL, Data & APIs','Deployment, Portfolio & Career']
 ]
];
require dirname(__DIR__).'/includes/learning-academy.php';
