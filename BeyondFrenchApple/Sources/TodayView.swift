import SwiftUI

struct TodayView: View {
    @EnvironmentObject private var store: AppStore

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 22) {
                BrandHeader()
                HStack {
                    AccessPill(text: "FREE LESSON OF THE DAY")
                    Spacer()
                    Text(store.statusMessage).font(.caption).foregroundStyle(.secondary)
                }
                LazyVGrid(columns: [.init(.flexible()), .init(.flexible()), .init(.flexible())], spacing: 10) {
                    MetricTile(title: "Words", value: "\(store.dictionary.count)", systemImage: "text.book.closed.fill", color: .teal)
                    MetricTile(title: "Modules", value: "\(store.academy.modules.count)", systemImage: "square.grid.2x2.fill", color: .indigo)
                    MetricTile(title: "Correct", value: "\(store.correctPracticeCount)", systemImage: "checkmark.circle.fill", color: .green)
                }
                VStack(alignment: .leading, spacing: 14) {
                    Text("TODAY'S PHRASE").font(.caption.bold()).tracking(2).foregroundStyle(.indigo)
                    Text(store.lesson.english).font(.system(size: 38, weight: .black, design: .rounded))
                    Divider()
                    Text(store.lesson.french).font(.system(size: 34, weight: .bold, design: .rounded)).foregroundStyle(.indigo)
                    Text(store.lesson.frenchPronunciation).font(.headline).foregroundStyle(.secondary)
                    HStack {
                        Button { store.speak(store.lesson.french) } label: {
                            Label("Listen", systemImage: "speaker.wave.2.fill").frame(maxWidth: .infinity)
                        }
                        .buttonStyle(.borderedProminent)
                        NavigationLink {
                            PracticeView()
                        } label: {
                            Label("Practice", systemImage: "pencil.and.outline").frame(maxWidth: .infinity)
                        }
                        .buttonStyle(.bordered)
                    }
                    .controlSize(.large)
                }
                .padding(22).background(.background, in: RoundedRectangle(cornerRadius: 22))
                .shadow(color: .black.opacity(0.08), radius: 20, y: 10)

                LazyVGrid(columns: [.init(.flexible()), .init(.flexible())], spacing: 12) {
                    LanguageTile(flag: "FR", name: "Francais", value: store.lesson.french, color: .indigo)
                    LanguageTile(flag: "🇭🇹", name: "Kreyòl", value: store.lesson.kreyol, color: .red)
                    LanguageTile(flag: "🇯🇲", name: "Patois", value: store.lesson.patois, color: .green)
                    LanguageTile(flag: "ES", name: "Espanol", value: store.lesson.spanish, color: .orange)
                }
                GroupBox("Culture note") {
                    Text(store.lesson.cultureNote).frame(maxWidth: .infinity, alignment: .leading)
                }
            }.padding()
        }
        .background(Color(uiColor: .systemGroupedBackground))
        .refreshable { await store.refreshLesson() }
        .navigationTitle("Today")
        .navigationBarTitleDisplayMode(.inline)
    }
}

private struct LanguageTile: View {
    let flag: String, name: String, value: String
    let color: Color
    var body: some View {
        VStack(alignment: .leading, spacing: 8) {
            Text(flag).font(.title3.weight(.black))
            Text(name.uppercased()).font(.caption.bold()).foregroundStyle(color)
            Text(value).font(.headline).frame(maxWidth: .infinity, alignment: .leading)
        }.padding().frame(minHeight: 132).background(color.opacity(0.09), in: RoundedRectangle(cornerRadius: 18))
    }
}
