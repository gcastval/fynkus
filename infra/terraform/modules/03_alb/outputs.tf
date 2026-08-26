
output "arn" {
  value = aws_lb.main.arn
}

output "dns_name" {
  value = aws_lb.main.dns_name
}

output "zone_id" {
  value = aws_lb.main.zone_id
}

output "alb_security_group_id" {
  value = aws_security_group.alb_sg.id
}

output "api_tg_arn" { 
  value = aws_lb_target_group.alb_api_tg.arn
}

