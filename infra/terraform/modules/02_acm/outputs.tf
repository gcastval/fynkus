
output "acm_arn" {
  value = aws_acm_certificate.main.arn
}

output "acm_validation" {
  value = aws_acm_certificate.main.domain_validation_options
}

output "acm_validation_record" {
  description = "DNS record to create at your external DNS provider to validate the certificate"
  value = {
    name  = tolist(aws_acm_certificate.main.domain_validation_options)[0].resource_record_name
    type  = tolist(aws_acm_certificate.main.domain_validation_options)[0].resource_record_type
    value = tolist(aws_acm_certificate.main.domain_validation_options)[0].resource_record_value
  }
}

output "acm_validated_arn" {
  value = aws_acm_certificate_validation.main.certificate_arn
}