document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".tab-link");
  const contents = document.querySelectorAll(".tab-content");

  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      // 1. Xử lý trạng thái Active của nút Tab
      tabs.forEach((t) => t.classList.remove("active"));
      this.classList.add("active");

      // 2. Ẩn toàn bộ các vùng nội dung
      contents.forEach((content) => {
        content.style.display = "none";
      });

      // 3. Hiển thị vùng nội dung tương ứng với data-target
      const targetId = this.getAttribute("data-target");
      const targetContent = document.getElementById(targetId);
      if (targetContent) {
        targetContent.style.display = "block";
      }
    });
  });
});
