output "alb_dns_name" {
  value = module.alb.dns_name
}

output "alb_zone_id" {
  value = module.alb.zone_id
}

output "acm_validation_record" {
  value = module.acm.acm_validation_record
}
