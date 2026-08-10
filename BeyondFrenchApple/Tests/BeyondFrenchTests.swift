import XCTest
@testable import BeyondFrench

final class BeyondFrenchTests: XCTestCase {
    func testFallbackLessonHasFrenchContent() {
        XCTAssertFalse(FrenchLesson.fallback.french.isEmpty)
        XCTAssertEqual(FrenchLesson.fallback.id, 1)
    }

    func testFallbackAcademyHasFreeGreetingLesson() {
        let module = AcademyCatalog.fallback.modules[0]

        XCTAssertEqual(module.slug, "greetings")
        XCTAssertTrue(module.isFree)
        XCTAssertEqual(module.lessons[0].french, "Bonjour !")
    }

    @MainActor
    func testAnswerCheckingIgnoresCasePunctuationAndAccents() {
        let store = AppStore()

        XCTAssertTrue(store.checkAnswer("  tres bien! ", expected: "Très bien"))
    }
}
