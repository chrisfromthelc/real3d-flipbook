import js from "@eslint/js";
import globals from "globals";
import json from "@eslint/json";
import markdown from "@eslint/markdown";
import css from "@eslint/css";
import { defineConfig } from "eslint/config";

export default defineConfig([
  {
    ignores: [
      "**/node_modules/**",
      "**/vendor/**",
      "**/*.min.js",
      "**/*.min.css",
      "**/js/libs/**",
      "**/lib/**",
      "**/tests/**",
    ],
  },
  {
    files: ["**/*.{js,mjs,cjs}"],
    plugins: { js },
    extends: ["js/recommended"],
    languageOptions: {
      globals: {
        ...globals.browser,
        jQuery: "readonly",
        wp: "readonly",
        ajaxurl: "readonly",
        FLIPBOOK: "writable",
        r3dfb: "readonly",
        r3d: "readonly",
        pdfjsLib: "readonly",
        Mark: "readonly",
        dataLayer: "writable",
        Swal: "readonly",
        YT: "readonly",
        wheelDeltaX: "readonly",
        wheelDeltaY: "readonly",
      },
    },
    rules: {
      "no-unused-vars": ["error", { args: "none", caughtErrors: "none" }],
      "no-case-declarations": "warn",
      "no-empty": ["error", { allowEmptyCatch: true }],
    },
  },
  { files: ["**/*.json"], plugins: { json }, language: "json/json", extends: ["json/recommended"] },
  { files: ["**/*.jsonc"], plugins: { json }, language: "json/jsonc", extends: ["json/recommended"] },
  { files: ["**/*.md"], plugins: { markdown }, language: "markdown/gfm", extends: ["markdown/recommended"] },
  { files: ["**/*.css"], plugins: { css }, language: "css/css", extends: ["css/recommended"] },
]);
