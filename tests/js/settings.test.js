require("./setup");

describe("Settings page utilities", () => {
  test("jQuery mock is available", () => {
    expect(global.jQuery).toBeDefined();
    expect(typeof global.jQuery).toBe("function");
  });

  test("wp globals are defined", () => {
    expect(global.wp).toBeDefined();
    expect(global.wp.i18n.__).toBeDefined();
    expect(global.wp.i18n.__("test")).toBe("test");
  });

  test("ajaxurl is defined", () => {
    expect(global.ajaxurl).toBe("/wp-admin/admin-ajax.php");
  });
});
