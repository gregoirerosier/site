<?php
declare(strict_types=1);
$academyConfig=[
 'slug'=>'beyond-math','title'=>'Beyond Math Academy','icon'=>'🧮','accent'=>'#1768ff','base'=>'/beyond-math/academy.php','css'=>'/beyond-math/academy.css','headline'=>'Math that grows with every learner.','description'=>'Six skill-based learning paths with guided practice, lesson tests, and module exams. Introductory lessons are free for everyone.',
 'tracks'=>[
  'early'=>['Numbers & Counting','Shapes & Space','Adding & Taking Away','Measurement & Time','Patterns & Money'],
  'foundations'=>['Number Sense','Operations','Fractions & Decimals','Geometry & Measurement','Data & Problem Solving'],
  'intermediate'=>['Integers & Rational Numbers','Ratios, Rates & Percent','Introduction to Algebra','Geometry & Coordinate Space','Statistics & Probability'],
  'advanced'=>['Algebra Foundations','Functions & Graphs','Geometry & Trigonometry','Statistics & Data Science','Precalculus'],
  'career'=>['Financial Math','Workplace Math','Data Literacy','College Preparation','Coding Math'],
  'adult'=>['Everyday Math','Money, Budgets & Interest','Skill Refresh','Practical Measurement','Personal Learning Plan']
 ]
];
require dirname(__DIR__).'/includes/learning-academy.php';
