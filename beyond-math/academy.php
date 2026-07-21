<?php
declare(strict_types=1);
$academyConfig=[
 'slug'=>'beyond-math','title'=>'Beyond Math Academy','icon'=>'🧮','accent'=>'#1768ff','base'=>'/beyond-math/academy.php','css'=>'/beyond-math/academy.css','headline'=>'Math that grows with every learner.','description'=>'Five modules with ten interactive lessons each, guided practice, lesson tests, and module exams. Module 1 is free for everyone.',
 'tracks'=>[
  'preschool'=>['Numbers & Counting','Shapes & Space','Adding & Taking Away','Measurement & Time','Patterns & Money'],
  'kids'=>['Number Sense','Operations','Fractions & Decimals','Geometry & Measurement','Data & Problem Solving'],
  'preteen'=>['Integers & Rational Numbers','Ratios, Rates & Percent','Introduction to Algebra','Geometry & Coordinate Space','Statistics & Probability'],
  'teen'=>['Algebra Foundations','Functions & Graphs','Geometry & Trigonometry','Statistics & Data Science','Precalculus'],
  'adult'=>['Everyday Math','Money, Budgets & Interest','Skill Refresh','Practical Measurement','Personal Learning Plan']
 ]
];
require dirname(__DIR__).'/includes/learning-academy.php';
