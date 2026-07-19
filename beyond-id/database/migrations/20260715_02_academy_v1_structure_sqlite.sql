PRAGMA foreign_keys=ON;
CREATE TABLE IF NOT EXISTS academy_module_exam_attempts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  course_id INTEGER NOT NULL,
  score INTEGER NOT NULL DEFAULT 0,
  question_count INTEGER NOT NULL DEFAULT 10,
  passed INTEGER NOT NULL DEFAULT 0 CHECK(passed IN(0,1)),
  answers_json TEXT NOT NULL DEFAULT '{}',
  attempted_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(course_id) REFERENCES academy_courses(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_academy_exam_access ON academy_module_exam_attempts(user_id,course_id,passed,attempted_at);

UPDATE academy_courses SET is_published=0 WHERE slug IN('commandments-audio-journey','bible-foundations');

WITH module_seed(age_slug,module_number,title,summary) AS (VALUES
 ('preschool',1,'God Made Me and Loves Me','Simple Bible truths about God, creation, love, safety, and belonging.'),
 ('preschool',2,'Bible Heroes','Ten story-based lessons about courage, trust, and God’s care.'),
 ('preschool',3,'Jesus Loves Children','Meet Jesus through welcoming, healing, helping, and kindness.'),
 ('preschool',4,'Prayer and Kindness','Practice short prayers, sharing, helping, and forgiveness.'),
 ('preschool',5,'Worship and Thankfulness','Celebrate God through gratitude, songs, wonder, and joy.'),
 ('kids',1,'The Ten Commandments','Learn God’s ten loving rules through stories and everyday choices.'),
 ('kids',2,'Bible Foundations','Understand the Bible, creation, covenant, prayer, and faith.'),
 ('kids',3,'The Life of Jesus','Follow the birth, teaching, miracles, death, and resurrection of Jesus.'),
 ('kids',4,'Prayer and Faith','Build habits of prayer, trust, courage, gratitude, and worship.'),
 ('kids',5,'Living with Courage','Apply faith to friendship, honesty, fear, service, and choices.'),
 ('preteen',1,'Ten Commandments in Real Life','Connect every commandment to school, family, identity, and digital life.'),
 ('preteen',2,'Identity and Purpose','Explore belonging, gifts, confidence, calling, and God-given worth.'),
 ('preteen',3,'Wisdom and Choices','Practice discernment in friendships, media, speech, pressure, and habits.'),
 ('preteen',4,'The Life and Teaching of Jesus','Study Jesus’ message, actions, parables, sacrifice, and resurrection.'),
 ('preteen',5,'Faith in Action','Serve others through compassion, justice, generosity, courage, and leadership.'),
 ('teen',1,'Identity in Christ','Build a grounded identity through grace, truth, purpose, and belonging.'),
 ('teen',2,'Relationships and Integrity','Navigate friendship, dating, sexuality, boundaries, honesty, and respect.'),
 ('teen',3,'Questions, Doubt, and Faith','Engage difficult questions with Scripture, humility, evidence, and trust.'),
 ('teen',4,'Purpose and Leadership','Develop gifts, vocation, service, resilience, influence, and responsibility.'),
 ('teen',5,'Faith in Culture','Think faithfully about media, justice, technology, community, and public life.'),
 ('adult',1,'Bible Foundations','Build a reliable framework for Scripture, the gospel, prayer, and discipleship.'),
 ('adult',2,'Spiritual Disciplines','Practice prayer, study, worship, fasting, generosity, solitude, and service.'),
 ('adult',3,'Relationships and Calling','Apply biblical wisdom to family, singleness, work, community, and vocation.'),
 ('adult',4,'Biblical Wisdom for Life','Navigate money, conflict, suffering, decisions, habits, and emotional health.'),
 ('adult',5,'Faithful Leadership','Lead with character, stewardship, courage, justice, humility, and multiplication.')
)
INSERT INTO academy_courses(slug,title,summary,is_free,is_published,sort_order)
SELECT age_slug||'-module-'||module_number,title,summary,CASE module_number WHEN 1 THEN 1 ELSE 0 END,1,
 CASE age_slug WHEN 'preschool' THEN 100 WHEN 'kids' THEN 200 WHEN 'preteen' THEN 300 WHEN 'teen' THEN 400 ELSE 500 END+module_number
FROM module_seed
WHERE 1
ON CONFLICT(slug) DO UPDATE SET title=excluded.title,summary=excluded.summary,is_free=excluded.is_free,is_published=1,sort_order=excluded.sort_order,updated_at=CURRENT_TIMESTAMP;

DELETE FROM academy_course_age_groups WHERE course_id IN(SELECT id FROM academy_courses WHERE slug GLOB '*-module-[1-5]');
INSERT OR IGNORE INTO academy_course_age_groups(course_id,age_group_id)
SELECT c.id,a.id FROM academy_courses c JOIN academy_age_groups a ON c.slug=a.slug||'-module-'||substr(c.slug,-1) WHERE c.slug GLOB '*-module-[1-5]';

WITH RECURSIVE numbers(n) AS (SELECT 1 UNION ALL SELECT n+1 FROM numbers WHERE n<10)
INSERT INTO academy_lessons(course_id,lesson_number,title,lesson_type,is_preview,is_published)
SELECT c.id,n.n,c.title||' · Lesson '||n.n,'reading',CASE c.is_free WHEN 1 THEN 1 ELSE 0 END,1
FROM academy_courses c CROSS JOIN numbers n WHERE c.slug GLOB '*-module-[1-5]'
ON CONFLICT(course_id,lesson_number) DO UPDATE SET title=excluded.title,lesson_type=excluded.lesson_type,is_preview=excluded.is_preview,is_published=1,updated_at=CURRENT_TIMESTAMP;

UPDATE academy_lessons SET title=CASE lesson_number
 WHEN 1 THEN 'Put God First' WHEN 2 THEN 'Worship God, Not Idols' WHEN 3 THEN 'Honor God’s Name'
 WHEN 4 THEN 'Make Time for Worship and Rest' WHEN 5 THEN 'Honor Parents and Caregivers'
 WHEN 6 THEN 'Protect Life and Choose Kindness' WHEN 7 THEN 'Keep Promises Faithfully'
 WHEN 8 THEN 'Respect What Belongs to Others' WHEN 9 THEN 'Tell the Truth'
 WHEN 10 THEN 'Practice Gratitude and Contentment' END
WHERE course_id IN(SELECT id FROM academy_courses WHERE slug IN('kids-module-1','preteen-module-1'));
