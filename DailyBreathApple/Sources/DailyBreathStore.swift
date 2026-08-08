import AVFoundation
import Foundation

@MainActor
final class DailyBreathStore: ObservableObject {
    @Published private(set) var verse = Verse.daily
    @Published private(set) var devotional = Devotional.today
    @Published var breathPhase = "Inhale"
    @Published var journalText = ""
    @Published var journalPrompt = DailyBreathStore.promptOfTheDay()
    @Published var journalMood = "Peaceful"
    @Published private(set) var entries: [JournalEntry] = []
    @Published private(set) var bibleLibrary = BibleLibrary.loadWorldEnglishBible()

    let practices = [
        PrayerPractice(id: 1, title: "Peace Breath", subtitle: "A four-count rhythm for calm and focus.", systemImage: "wind"),
        PrayerPractice(id: 2, title: "Guidance Prayer", subtitle: "Pause before decisions and ask for wisdom.", systemImage: "hands.sparkles.fill"),
        PrayerPractice(id: 3, title: "Gratitude Reset", subtitle: "Name what is good before the day moves on.", systemImage: "heart.fill"),
        PrayerPractice(id: 4, title: "Weekly Challenge", subtitle: "Turn faith into one practical action.", systemImage: "calendar.badge.checkmark")
    ]

    private let speaker = AVSpeechSynthesizer()

    func speakVerse() {
        speaker.stopSpeaking(at: .immediate)
        let utterance = AVSpeechUtterance(string: "\(verse.text) \(verse.reference)")
        utterance.voice = AVSpeechSynthesisVoice(language: "en-CA")
        utterance.rate = 0.42
        speaker.speak(utterance)
    }

    func saveJournalEntry() {
        let trimmed = journalText.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !trimmed.isEmpty else { return }
        entries.insert(
            JournalEntry(
                id: UUID(),
                createdAt: Date(),
                prompt: journalPrompt,
                text: trimmed,
                mood: journalMood
            ),
            at: 0
        )
        journalText = ""
    }

    func prepareJournalReflection(prompt: String, text: String = "", mood: String? = nil) {
        journalPrompt = prompt
        journalText = text
        if let mood {
            journalMood = mood
        }
    }

    func updateJournalEntry(_ entry: JournalEntry, text: String, mood: String?) {
        let trimmed = text.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !trimmed.isEmpty, let index = entries.firstIndex(where: { $0.id == entry.id }) else { return }
        entries[index] = JournalEntry(
            id: entry.id,
            createdAt: entry.createdAt,
            prompt: entry.prompt,
            text: trimmed,
            mood: mood
        )
    }

    func deleteJournalEntries(at offsets: IndexSet) {
        entries.remove(atOffsets: offsets)
    }

    static func promptOfTheDay(for date: Date = Date(), calendar: Calendar = .current) -> String {
        let prompts = [
            "Where do I need to practice stillness today?",
            "What is one faithful next step I can take?",
            "What am I carrying that I can entrust to God?",
            "Where did I notice grace today?",
            "Who needs patience, courage, or kindness from me?"
        ]
        let day = calendar.ordinality(of: .day, in: .era, for: date) ?? 1
        return prompts[(day - 1) % prompts.count]
    }
}
