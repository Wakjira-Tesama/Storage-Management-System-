function showSection(id, el = null) {
  const sections = document.querySelectorAll(".section");
  sections.forEach((sec) => (sec.style.display = "none"));
  document.getElementById(id).style.display = "block";

  // Handle active tab link
  const links = document.querySelectorAll(".tab-link");
  links.forEach((link) => link.classList.remove("active"));
  if (el) el.classList.add("active");
}

// Show default section on page load
document.addEventListener("DOMContentLoaded", () => {
  const accountTab = document.querySelector('.tab-link[data-target="account"]');
  const addItemTab = document.querySelector('.tab-link[data-target="additem"]');
  const newItemTab = document.querySelector('.tab-link[data-target="newitem"]');
  const viewItemTab = document.querySelector('.tab-link[data-target="AI"]');
  const branchItemTab = document.querySelector('.tab-link[data-target="mbs"]');
  const buyItemTab = document.querySelector('.tab-link[data-target="tore"]');

  if (accountTab) showSection("account", accountTab);
  if (addItemTab) showSection("additem", addItemTab);
  if (viewItemTab) showSection("AI", viewItemTab);
  if (newItemTab) showSection("newitem", newItemTab);
  if (branchItemTab) showSection("mbs", branchItemTab);
  if (buyItemTab) showSection("tore", buyItemTab);
});
