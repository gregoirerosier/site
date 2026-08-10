import SwiftUI

struct AcademyView: View {
    @EnvironmentObject private var store: AppStore
    @State private var selectedAge = "kids"

    private var selectedGroup: AgeGroup {
        store.academy.ageGroups.first { $0.slug == selectedAge } ?? store.academy.ageGroups.first ?? AcademyCatalog.fallback.ageGroups[0]
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 20) {
                VStack(alignment: .leading, spacing: 14) {
                    AccessPill(text: store.hasBeyondID ? "FULL ACADEMY" : "GREETINGS PREVIEW")
                    Text("Choose a path and start speaking.")
                        .font(.largeTitle.weight(.black))
                    Text(selectedGroup.guidance)
                        .font(.subheadline)
                        .foregroundStyle(.secondary)
                    Picker("Learner age", selection: $selectedAge) {
                        ForEach(store.academy.ageGroups) { group in
                            Text("\(group.title) \(group.ages)").tag(group.slug)
                        }
                    }
                    .pickerStyle(.menu)
                }
                .padding(20)
                .frame(maxWidth: .infinity, alignment: .leading)
                .background(.background, in: RoundedRectangle(cornerRadius: 22))

                ForEach(store.academy.modules) { module in
                    ModuleCard(module: module)
                }
            }
            .padding()
        }
        .background(Color(uiColor: .systemGroupedBackground))
        .navigationTitle("Academy")
    }
}

private struct ModuleCard: View {
    @EnvironmentObject private var store: AppStore
    let module: AcademyModule

    private var completedCount: Int {
        module.lessons.indices.filter { store.isLessonCompleted(module: module, lessonIndex: $0) }.count
    }

    var body: some View {
        VStack(alignment: .leading, spacing: 16) {
            HStack(alignment: .top, spacing: 14) {
                Text(module.icon)
                    .font(.largeTitle)
                    .frame(width: 52, height: 52)
                    .background(.indigo.opacity(0.10), in: RoundedRectangle(cornerRadius: 16))
                VStack(alignment: .leading, spacing: 5) {
                    HStack {
                        Text(module.title)
                            .font(.title3.weight(.black))
                        Spacer()
                        if module.isFree {
                            Text("FREE").font(.caption2.weight(.black)).foregroundStyle(.green)
                        }
                    }
                    Text(module.description)
                        .font(.subheadline)
                        .foregroundStyle(.secondary)
                }
            }

            SwiftUI.ProgressView(value: Double(completedCount), total: Double(max(module.lessons.count, 1)))
                .tint(.indigo)

            VStack(spacing: 8) {
                ForEach(Array(module.lessons.enumerated()), id: \.offset) { index, lesson in
                    LessonRow(module: module, lesson: lesson, lessonIndex: index)
                }
            }
        }
        .padding(18)
        .background(.background, in: RoundedRectangle(cornerRadius: 22))
    }
}

private struct LessonRow: View {
    @EnvironmentObject private var store: AppStore
    let module: AcademyModule
    let lesson: AcademyLesson
    let lessonIndex: Int

    private var isUnlocked: Bool {
        store.isLessonUnlocked(module: module, lessonIndex: lessonIndex)
    }

    var body: some View {
        NavigationLink {
            AcademyLessonDetailView(module: module, lesson: lesson, lessonIndex: lessonIndex)
        } label: {
            HStack(spacing: 12) {
                Text("\(lessonIndex + 1)")
                    .font(.headline.weight(.black))
                    .foregroundStyle(isUnlocked ? .indigo : .secondary)
                    .frame(width: 34, height: 34)
                    .background((isUnlocked ? Color.indigo : Color.secondary).opacity(0.10), in: Circle())
                VStack(alignment: .leading, spacing: 3) {
                    Text(lesson.title).font(.subheadline.weight(.bold))
                    Text(lesson.english).font(.caption).foregroundStyle(.secondary)
                }
                Spacer()
                Image(systemName: store.isLessonCompleted(module: module, lessonIndex: lessonIndex) ? "checkmark.circle.fill" : (isUnlocked ? "chevron.right" : "lock.fill"))
                    .foregroundStyle(store.isLessonCompleted(module: module, lessonIndex: lessonIndex) ? .green : .secondary)
            }
            .contentShape(Rectangle())
            .opacity(isUnlocked ? 1 : 0.55)
        }
        .disabled(!isUnlocked)
        .buttonStyle(.plain)
    }
}

private struct AcademyLessonDetailView: View {
    @EnvironmentObject private var store: AppStore
    let module: AcademyModule
    let lesson: AcademyLesson
    let lessonIndex: Int

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 18) {
                Text(module.icon)
                    .font(.largeTitle)
                Text(lesson.title)
                    .font(.largeTitle.weight(.black))
                Text(lesson.english)
                    .font(.title3)
                    .foregroundStyle(.secondary)

                VStack(alignment: .leading, spacing: 12) {
                    Text(lesson.french)
                        .font(.system(size: 36, weight: .black, design: .rounded))
                        .foregroundStyle(.indigo)
                    Text(lesson.pronunciation)
                        .font(.headline)
                        .foregroundStyle(.secondary)
                    Button { store.speak(lesson.french) } label: {
                        Label("Listen in French", systemImage: "speaker.wave.2.fill").frame(maxWidth: .infinity)
                    }
                    .buttonStyle(.borderedProminent)
                    .controlSize(.large)
                }
                .padding(20)
                .background(.indigo.opacity(0.08), in: RoundedRectangle(cornerRadius: 20))

                LessonInfoBlock(title: "Teaching", text: lesson.teaching, systemImage: "lightbulb.fill", color: .yellow)
                LessonInfoBlock(title: "Practice", text: lesson.practice, systemImage: "person.wave.2.fill", color: .teal)
                LessonInfoBlock(title: "Culture", text: lesson.culture, systemImage: "globe.americas.fill", color: .orange)

                Button {
                    store.completeLesson(module: module, lessonIndex: lessonIndex)
                } label: {
                    Label(
                        store.isLessonCompleted(module: module, lessonIndex: lessonIndex) ? "Lesson Complete" : "Mark Complete",
                        systemImage: "checkmark.seal.fill"
                    )
                    .frame(maxWidth: .infinity)
                }
                .buttonStyle(.borderedProminent)
                .controlSize(.large)
            }
            .padding()
        }
        .background(Color(uiColor: .systemGroupedBackground))
        .navigationTitle("Lesson \(lessonIndex + 1)")
        .navigationBarTitleDisplayMode(.inline)
    }
}

struct LessonInfoBlock: View {
    let title: String
    let text: String
    let systemImage: String
    let color: Color

    var body: some View {
        HStack(alignment: .top, spacing: 12) {
            Image(systemName: systemImage)
                .foregroundStyle(color)
                .frame(width: 24)
            VStack(alignment: .leading, spacing: 4) {
                Text(title).font(.headline)
                Text(text).foregroundStyle(.secondary)
            }
            Spacer()
        }
        .padding(16)
        .background(.background, in: RoundedRectangle(cornerRadius: 18))
    }
}
