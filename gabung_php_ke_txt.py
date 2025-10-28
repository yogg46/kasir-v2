import os

# === INPUT USER ===
folder_path = input("Masukkan path folder yang ingin diproses: ").strip()
output_name = input("Masukkan nama file output (tanpa .txt): ").strip()

# Daftar ekstensi file yang ingin digabung
extensions = [".php", ".js", ".jsx", ".css", ".html", ".json", ".ts", ".tsx"]

# Normalisasi path
folder_path = os.path.abspath(folder_path)
output_file = f"{output_name}.txt"

print(f"\n📂 Path folder yang dipakai: {folder_path}")
print(f"📄 Ekstensi yang dicari: {', '.join(extensions)}\n")

# Cek apakah folder ada
if not os.path.isdir(folder_path):
    print("❌ Folder tidak ditemukan! Pastikan path benar.")
    exit()

# === PROSES FILE ===
with open(output_file, "w", encoding="utf-8") as outfile:
    total_files = 0
    for root, dirs, files in os.walk(folder_path):
        for file in files:
            if any(file.endswith(ext) for ext in extensions):
                file_path = os.path.join(root, file)
                try:
                    with open(file_path, "r", encoding="utf-8", errors="ignore") as infile:
                        outfile.write(f"\n\n=== FILE: {file_path} ===\n\n")
                        outfile.write(infile.read())
                        total_files += 1
                except Exception as e:
                    print(f"⚠️ Gagal membaca {file_path}: {e}")

if total_files == 0:
    print("⚠️ Tidak ada file dengan ekstensi yang cocok ditemukan.")
else:
    print(f"\n✅ Berhasil menggabungkan {total_files} file ke '{output_file}'")
