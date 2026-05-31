import { existsSync } from "node:fs";
import path from "node:path";
import { fileURLToPath, pathToFileURL } from "node:url";
import { defineConfig, mergeConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(async () => {
    const config = {
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                refresh: true,
                fonts: [
                    bunny("Instrument Sans", {
                        weights: [400, 500, 600],
                    }),
                ],
            }),
            tailwindcss(),
        ],
        server: {
            host: "127.0.0.1",
            watch: {
                ignored: ["**/storage/framework/views/**"],
            },
        },
    };

    const localConfigPath = path.join(
        path.dirname(fileURLToPath(import.meta.url)),
        "vite.config.local.js",
    );

    if (existsSync(localConfigPath)) {
        const { default: local } = await import(
            pathToFileURL(localConfigPath).href
        );

        return mergeConfig(config, local);
    }

    return config;
});
