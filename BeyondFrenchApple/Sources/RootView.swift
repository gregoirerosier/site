import SwiftUI

struct RootView: View {
    @EnvironmentObject private var store: AppStore

    var body: some View {
        TabView {
            NavigationStack { TodayView() }
                .tabItem { Label("Today", systemImage: "sun.max.fill") }
            NavigationStack { AcademyView() }
                .tabItem { Label("Academy", systemImage: "graduationcap.fill") }
            NavigationStack { DictionaryView() }
                .tabItem { Label("Dictionary", systemImage: "character.book.closed.fill") }
            NavigationStack { PracticeView() }
                .tabItem { Label("Practice", systemImage: "waveform.and.mic") }
            NavigationStack { LearningProgressView() }
                .tabItem { Label("Progress", systemImage: "chart.bar.fill") }
        }
        .tint(store.appTheme.accent)
    }
}

struct BrandHeader: View {
    @EnvironmentObject private var store: AppStore

    var body: some View {
        HStack(spacing: 12) {
            Image("BeyondFrenchLogo").resizable().scaledToFit().frame(width: 54, height: 54).clipShape(Circle())
            VStack(alignment: .leading, spacing: 2) {
                Text("BEYOND FRENCH").font(.headline.weight(.black))
                Text("Speak, listen, remember").font(.caption).foregroundStyle(.secondary)
            }
            Spacer()
            Image(systemName: store.appTheme.symbol)
                .font(.headline)
                .foregroundStyle(store.appTheme.accent)
                .frame(width: 34, height: 34)
                .background(store.appTheme.accent.opacity(0.12), in: Circle())
        }
    }
}

struct AccessPill: View {
    let text: String
    var body: some View {
        Label(text, systemImage: "checkmark.seal.fill")
            .font(.caption.bold()).foregroundStyle(.green)
            .padding(.horizontal, 10).padding(.vertical, 7)
            .background(.green.opacity(0.11), in: Capsule())
    }
}

struct MetricTile: View {
    let title: String
    let value: String
    let systemImage: String
    let color: Color

    var body: some View {
        VStack(alignment: .leading, spacing: 10) {
            Image(systemName: systemImage)
                .font(.title3.weight(.semibold))
                .foregroundStyle(color)
            Text(value)
                .font(.title2.weight(.black))
            Text(title.uppercased())
                .font(.caption2.weight(.bold))
                .foregroundStyle(.secondary)
        }
        .frame(maxWidth: .infinity, alignment: .leading)
        .padding(14)
        .background(color.opacity(0.10), in: RoundedRectangle(cornerRadius: 16))
    }
}

struct ThemePicker: View {
    @EnvironmentObject private var store: AppStore

    var body: some View {
        VStack(alignment: .leading, spacing: 12) {
            Text("Theme")
                .font(.title3.weight(.black))
            Picker("Theme", selection: $store.appTheme) {
                ForEach(AppTheme.allCases) { theme in
                    Label(theme.title, systemImage: theme.symbol).tag(theme)
                }
            }
            .pickerStyle(.segmented)
        }
        .padding(18)
        .background(.background, in: RoundedRectangle(cornerRadius: 22))
    }
}

extension AppTheme {
    var accent: Color {
        switch self {
        case .classic: .indigo
        case .ocean: .teal
        case .sunrise: .orange
        case .garden: .green
        }
    }

    var symbol: String {
        switch self {
        case .classic: "sparkles"
        case .ocean: "water.waves"
        case .sunrise: "sun.max.fill"
        case .garden: "leaf.fill"
        }
    }
}
