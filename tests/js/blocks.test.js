require("./setup");

describe("Real3D Flipbook Gutenberg Block", () => {
  beforeEach(() => {
    wp.blocks.registerBlockType.mockClear();
    global.r3dfb = [
      { id: "1", name: "Test Book" },
      { id: "2", name: "Another Book" },
    ];
    global.wp.element.RawHTML = "RawHTML";
  });

  test("registers the r3dfb/embed block", () => {
    require("../../js/blocks.js");
    expect(wp.blocks.registerBlockType).toHaveBeenCalledWith(
      "r3dfb/embed",
      expect.any(Object),
    );
  });

  test("block has expected attributes", () => {
    require("../../js/blocks.js");
    const blockConfig = wp.blocks.registerBlockType.mock.calls[0][1];

    expect(blockConfig.attributes).toHaveProperty("id");
    expect(blockConfig.attributes.id.type).toBe("string");
    expect(blockConfig.attributes).toHaveProperty("pdf");
    expect(blockConfig.attributes.pdf.type).toBe("string");
    expect(blockConfig.attributes).toHaveProperty("mode");
    expect(blockConfig.attributes.mode.default).toBe("normal");
    expect(blockConfig.attributes).toHaveProperty("pages");
  });

  test("block has edit and save functions", () => {
    require("../../js/blocks.js");
    const blockConfig = wp.blocks.registerBlockType.mock.calls[0][1];

    expect(typeof blockConfig.edit).toBe("function");
    expect(typeof blockConfig.save).toBe("function");
  });

  test("block title is Real3D FlipBook", () => {
    require("../../js/blocks.js");
    const blockConfig = wp.blocks.registerBlockType.mock.calls[0][1];

    expect(blockConfig.title).toBe("Real3D FlipBook");
  });

  test("block category is media", () => {
    require("../../js/blocks.js");
    const blockConfig = wp.blocks.registerBlockType.mock.calls[0][1];

    expect(blockConfig.category).toBe("media");
  });

  test("save function generates shortcode string", () => {
    require("../../js/blocks.js");
    const blockConfig = wp.blocks.registerBlockType.mock.calls[0][1];

    const result = blockConfig.save({
      attributes: { id: "42", mode: "lightbox" },
    });

    expect(result).not.toBeNull();
  });
});
