import Foundation

struct TodayResponse: Decodable {
    let ok: Bool
    let date: String
    let lesson: FrenchLesson
}

struct FrenchLesson: Codable, Identifiable, Hashable {
    let id: Int
    let date: String?
    let english: String
    let french: String
    let frenchPronunciation: String
    let patois: String
    let kreyol: String
    let spanish: String
    let meaning: String
    let cultureNote: String
    let challenge: String
    let answer: String

    enum CodingKeys: String, CodingKey {
        case id, date, english, french, patois, kreyol, spanish, meaning, challenge, answer
        case frenchPronunciation = "french_pronunciation"
        case cultureNote = "culture_note"
    }

    static let fallback = FrenchLesson(
        id: 1, date: nil, english: "Keep going.", french: "Continue.",
        frenchPronunciation: "Kohn-tee-new", patois: "Keep on gwaan.",
        kreyol: "Kontinye.", spanish: "Sigue adelante.",
        meaning: "A way to encourage someone to continue.",
        cultureNote: "A little encouragement can go a long way.",
        challenge: "How would you say “Keep going.” in French?", answer: "Continue."
    )
}

struct DictionaryWord: Codable, Identifiable, Hashable {
    var id: String { english + french }
    let english: String
    let french: String
    let pronunciation: String
    let spanish: String
    let kreyol: String
    let patois: String
    let type: String
}

struct AcademyCatalog: Codable {
    let ageGroups: [AgeGroup]
    let modules: [AcademyModule]

    enum CodingKeys: String, CodingKey {
        case ageGroups = "age_groups"
        case modules
    }

    static let fallback = AcademyCatalog(
        ageGroups: [
            AgeGroup(slug: "kids", title: "Kids", ages: "6-9", icon: "palette", guidance: "Learn through short role-play, drawing, matching, and speaking aloud.")
        ],
        modules: [
            AcademyModule(
                slug: "greetings",
                title: "Greetings",
                icon: "hand.wave.fill",
                isFree: true,
                description: "Meet people, introduce yourself, and close conversations with confidence.",
                lessons: [
                    AcademyLesson(
                        title: "Hello and Goodbye",
                        english: "Hello!",
                        french: "Bonjour !",
                        pronunciation: "bohn-zhoor",
                        teaching: "Use bonjour during the day with friends, teachers, coworkers, and new people.",
                        practice: "Greet three imaginary people with a smile and say bonjour clearly.",
                        culture: "A greeting is expected when entering many shops and small businesses in French-speaking places."
                    )
                ]
            )
        ]
    )
}

struct AgeGroup: Codable, Identifiable, Hashable {
    var id: String { slug }
    let slug: String
    let title: String
    let ages: String
    let icon: String
    let guidance: String
}

struct AcademyModule: Codable, Identifiable, Hashable {
    var id: String { slug }
    let slug: String
    let title: String
    let icon: String
    let isFree: Bool
    let description: String
    let lessons: [AcademyLesson]

    enum CodingKeys: String, CodingKey {
        case slug, title, icon, description, lessons
        case isFree = "free"
    }
}

struct AcademyLesson: Codable, Identifiable, Hashable {
    var id: String { title + french }
    let title: String
    let english: String
    let french: String
    let pronunciation: String
    let teaching: String
    let practice: String
    let culture: String
}
