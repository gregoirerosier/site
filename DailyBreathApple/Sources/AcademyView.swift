import SwiftUI

struct AcademyView: View {
    @Environment(\.openURL) private var openURL
    @AppStorage("dailyBreathTheme") private var selectedThemeID = DailyBreathTheme.forest.id

    private let beyondIDLoginURL = URL(string: "https://beyondimagination.co.technology/beyond-id/auth/login.php?app=dailybreath")!
    private let previewPaths = [
        ("Foundations of Faith", "Prayer, Scripture, reflection, and daily practice."),
        ("Life of Jesus", "A guided path through the Gospels."),
        ("Wisdom and Prayer", "Psalms, Proverbs, and everyday devotion.")
    ]

    private var selectedTheme: DailyBreathTheme {
        DailyBreathTheme(id: selectedThemeID)
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 18) {
                hero
                preview
            }
            .padding()
        }
        .background(DailyBreathThemeBackground(theme: selectedTheme))
        .navigationTitle("Academy")
        .navigationBarTitleDisplayMode(.inline)
    }

    private var hero: some View {
        VStack(alignment: .leading, spacing: 16) {
            Label("Beyond ID", systemImage: "person.badge.key.fill")
                .font(.caption.bold())
                .foregroundStyle(selectedTheme.accent)
            Text("Bible Academy")
                .font(.largeTitle.weight(.black))
            Text("Coming Fall 2026")
                .font(.title2.weight(.bold))
                .foregroundStyle(selectedTheme.primary)
            Text("Guided Bible learning will unlock through Beyond ID when Academy content is ready. This version does not include lessons, progress, or account-gated content.")
                .foregroundStyle(.secondary)
            Button {
                openURL(beyondIDLoginURL)
            } label: {
                Label("Sign In with Beyond ID", systemImage: "person.badge.key.fill")
                    .frame(maxWidth: .infinity)
            }
            .buttonStyle(.borderedProminent)
            .tint(selectedTheme.primary)
            .controlSize(.large)
        }
        .padding(20)
        .background(.background.opacity(0.9), in: RoundedRectangle(cornerRadius: 8))
    }

    private var preview: some View {
        VStack(alignment: .leading, spacing: 12) {
            Text("Planned Paths")
                .font(.headline)
            ForEach(previewPaths, id: \.0) { path in
                HStack(spacing: 12) {
                    Image(systemName: "lock.fill")
                        .foregroundStyle(selectedTheme.accent)
                        .frame(width: 24)
                    VStack(alignment: .leading, spacing: 3) {
                        Text(path.0)
                            .font(.subheadline.weight(.semibold))
                        Text(path.1)
                            .font(.caption)
                            .foregroundStyle(.secondary)
                    }
                }
                .padding(12)
                .frame(maxWidth: .infinity, alignment: .leading)
                .background(.background.opacity(0.86), in: RoundedRectangle(cornerRadius: 8))
            }
        }
    }
}
