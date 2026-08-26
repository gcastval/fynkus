
resource "aws_security_group" "alb_sg" {
  name        = "alb_sg"
  description = "Security group for the ALB"
  vpc_id      = var.vpc_id

  tags = {
    Name = "allow_tls"
  }
}

resource "aws_vpc_security_group_ingress_rule" "alb_sg_ingress_http" {
  security_group_id = aws_security_group.alb_sg.id
  from_port         = 80
  ip_protocol       = "tcp"
  to_port           = 80
  cidr_ipv4         = "0.0.0.0/0"
}

resource "aws_vpc_security_group_ingress_rule" "alb_sg_ingress_https" {
  security_group_id = aws_security_group.alb_sg.id
  from_port         = 443
  ip_protocol       = "tcp"
  to_port           = 443
  cidr_ipv4         = "0.0.0.0/0"
}

resource "aws_vpc_security_group_egress_rule" "alb_sg_egress_all" {
  security_group_id = aws_security_group.alb_sg.id
  ip_protocol        = "-1"
  cidr_ipv4          = "0.0.0.0/0"

  description = "Allow all outbound traffic"
}


resource "aws_lb" "main" {
  name               = "main-alb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.alb_sg.id]
  subnets            = var.public_subnets_ids
}


resource "aws_lb_target_group" "alb_api_tg" {
  name                 = "alb-tg"
  vpc_id               = var.vpc_id
  port                 = 8000
  protocol             = "HTTP"
  target_type          = "ip"

  health_check {
    interval            = 30
    path                = "/api/v1/health-check"
    timeout             = 5
  }
}


resource "aws_lb_listener" "alb_http_listener" {
  load_balancer_arn = aws_lb.main.arn
  port              = "80"
  protocol          = "HTTP"

  default_action {
    type             = "redirect"
    
    redirect {
      port        = "443"
      protocol    = "HTTPS"
      status_code = "HTTP_301"
    }
  }
}


resource "aws_lb_listener" "alb_https_listener" {
  load_balancer_arn = aws_lb.main.arn
  port              = "443"
  protocol          = "HTTPS"
  certificate_arn   = var.acm_arn
  ssl_policy        = "ELBSecurityPolicy-2016-08"

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.alb_api_tg.arn
  }
}