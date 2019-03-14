workflow "New workflow" {
  on = "push"
  resolves = ["pxgamer/composer-action"]
}

action "pxgamer/composer-action" {
  uses = "pxgamer/composer-action"
  args = "install"
}
