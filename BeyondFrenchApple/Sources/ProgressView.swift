import SwiftUI

struct LearningProgressView: View {
    @EnvironmentObject private var store: AppStore

    private var totalLessons: Int {
        store.academy.modules.reduce(0) { $0 + $1.lessons.count }
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 18) {
                BrandHeader()

                LazyVGrid(columns: [.init(.flexible()), .init(.flexible())], spacing: 12) {
                    MetricTile(title: "Completed", value: "\(store.completedLessonIDs.count)/\(totalLessons)", systemImage: "checkmark.seal.fill", color: .green)
                    MetricTile(title: "Practice", value: "\(store.correctPracticeCount)", systemImage: "bolt.fill", color: .orange)
                    MetricTile(title: "Dictionary", value: "\(store.dictionary.count)", systemImage: "character.book.closed.fill", color: .teal)
                    MetricTile(title: "Access", value: store.hasBeyondID ? "Full" : "Free", systemImage: "person.crop.circle.badge.checkmark", color: .indigo)
                }

                VStack(alignment: .leading, spacing: 12) {
                    Text("Module Progress")
                        .font(.title3.weight(.black))
                    ForEach(store.academy.modules) { module in
                        ModuleProgressRow(module: module)
                    }
                }
                .padding(18)
                .background(.background, in: RoundedRectangle(cornerRadius: 22))

                if !store.hasBeyondID {
                    VStack(alignment: .leading, spacing: 12) {
                        Label("Continue with Beyond ID", systemImage: "lock.open.fill")
                            .font(.headline)
                        Text("Sign in to save cloud progress, lesson tests, Academy exams, and bit$ rewards.")
                            .foregroundStyle(.secondary)
                        Link(destination: URL(string: "https://beyondimagination.co.technology/beyond-id/auth/login.php?app=beyond-french")!) {
                            Label("Create or sign in", systemImage: "person.crop.circle.fill.badge.plus")
                                .frame(maxWidth: .infinity)
                        }
                        .buttonStyle(.borderedProminent)
                        .controlSize(.large)
                    }
                    .padding(18)
                    .background(.indigo.opacity(0.08), in: RoundedRectangle(cornerRadius: 22))
                }
            }
            .padding()
        }
        .background(Color(uiColor: .systemGroupedBackground))
        .navigationTitle("Progress")
    }
}

private struct ModuleProgressRow: View {
    @EnvironmentObject private var store: AppStore
    let module: AcademyModule

    private var completed: Int {
        module.lessons.indices.filter { store.isLessonCompleted(module: module, lessonIndex: $0) }.count
    }

    var body: some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack {
                Text("\(module.icon) \(module.title)")
                    .font(.subheadline.weight(.bold))
                Spacer()
                Text("\(completed)/\(module.lessons.count)")
                    .font(.caption.weight(.bold))
                    .foregroundStyle(.secondary)
            }
            SwiftUI.ProgressView(value: Double(completed), total: Double(max(module.lessons.count, 1)))
                .tint(module.isFree ? .green : .indigo)
        }
    }
}
