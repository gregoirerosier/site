import AVFoundation
import Combine
import Foundation

@MainActor
final class AppStore: ObservableObject {
    @Published private(set) var lesson = FrenchLesson.fallback
    @Published private(set) var dictionary: [DictionaryWord] = []
    @Published private(set) var academy = AcademyCatalog.fallback
    @Published private(set) var completedLessonIDs: Set<String> = []
    @Published private(set) var correctPracticeCount = 0
    @Published private(set) var isRefreshing = false
    @Published private(set) var statusMessage = "Free daily lesson"
    @Published var hasBeyondID = false

    private let endpoint = URL(string: "https://beyondimagination.co.technology/beyond-french/api/today.php")!
    private let speaker = AVSpeechSynthesizer()
    private let completedKey = "BeyondFrench.completedLessonIDs"
    private let practiceKey = "BeyondFrench.correctPracticeCount"

    func load() async {
        loadDictionary()
        loadAcademy()
        loadProgress()
        await refreshLesson()
    }

    func refreshLesson() async {
        isRefreshing = true
        defer { isRefreshing = false }
        do {
            let (data, response) = try await URLSession.shared.data(from: endpoint)
            guard let http = response as? HTTPURLResponse, http.statusCode == 200 else { throw URLError(.badServerResponse) }
            lesson = try JSONDecoder().decode(TodayResponse.self, from: data).lesson
            statusMessage = "Updated from Daily Studio"
        } catch {
            statusMessage = "Offline lesson"
        }
    }

    func speak(_ text: String, language: String = "fr-FR") {
        speaker.stopSpeaking(at: .immediate)
        let utterance = AVSpeechUtterance(string: text)
        utterance.voice = AVSpeechSynthesisVoice(language: language)
        utterance.rate = 0.43
        speaker.speak(utterance)
    }

    func isLessonCompleted(module: AcademyModule, lessonIndex: Int) -> Bool {
        completedLessonIDs.contains(lessonID(module: module, lessonIndex: lessonIndex))
    }

    func isLessonUnlocked(module: AcademyModule, lessonIndex: Int) -> Bool {
        guard lessonIndex > 0 else { return module.isFree || hasBeyondID }
        if !module.isFree && !hasBeyondID { return false }
        return hasBeyondID || isLessonCompleted(module: module, lessonIndex: lessonIndex - 1)
    }

    func completeLesson(module: AcademyModule, lessonIndex: Int) {
        completedLessonIDs.insert(lessonID(module: module, lessonIndex: lessonIndex))
        saveProgress()
    }

    func checkAnswer(_ answer: String, expected: String) -> Bool {
        normalized(answer) == normalized(expected)
    }

    func recordCorrectPractice() {
        correctPracticeCount += 1
        saveProgress()
    }

    private func loadDictionary() {
        guard let url = Bundle.main.url(forResource: "dictionary", withExtension: "json"),
              let data = try? Data(contentsOf: url),
              let words = try? JSONDecoder().decode([DictionaryWord].self, from: data) else { return }
        dictionary = words
    }

    private func loadAcademy() {
        guard let url = Bundle.main.url(forResource: "academy", withExtension: "json"),
              let data = try? Data(contentsOf: url),
              let catalog = try? JSONDecoder().decode(AcademyCatalog.self, from: data) else { return }
        academy = catalog
    }

    private func loadProgress() {
        completedLessonIDs = Set(UserDefaults.standard.stringArray(forKey: completedKey) ?? [])
        correctPracticeCount = UserDefaults.standard.integer(forKey: practiceKey)
    }

    private func saveProgress() {
        UserDefaults.standard.set(Array(completedLessonIDs), forKey: completedKey)
        UserDefaults.standard.set(correctPracticeCount, forKey: practiceKey)
    }

    private func lessonID(module: AcademyModule, lessonIndex: Int) -> String {
        "\(module.slug)-\(lessonIndex + 1)"
    }

    private func normalized(_ value: String) -> String {
        value
            .folding(options: [.caseInsensitive, .diacriticInsensitive], locale: .current)
            .replacingOccurrences(of: "[^a-z0-9]+", with: "", options: .regularExpression)
    }
}
