data "aws_iam_policy_document" "ecs_trust_policy" {
  statement {
    effect = "Allow"

    principals {
      type        = "Service"
      identifiers = ["ecs-tasks.amazonaws.com"]
    }

    actions = ["sts:AssumeRole"]
  }
}

resource "aws_iam_role" "ecs_task_execution_role" {
  name               = "ecs-task-execution-role"
  assume_role_policy = data.aws_iam_policy_document.ecs_trust_policy.json
}

resource "aws_iam_role_policy_attachment" "ecs_role_policy_attachment" {
  role       = aws_iam_role.ecs_task_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_ecs_cluster" "main" {
  name = "ecs-cluster"
}

resource "aws_cloudwatch_log_group" "task_log_group" {
  name = "/ecs/ecs-backend-task"

  tags = {
    Application = "ecs-task-test"
  }
}

resource "aws_ecs_task_definition" "backend" {
  family = var.task_definition.family
  cpu = var.task_definition.cpu
  memory = var.task_definition.memory
  network_mode = var.task_definition.networkMode
  requires_compatibilities = var.task_definition.requiresCompatibilities

  container_definitions = jsonencode(var.task_definition.containerDefinitions)

  execution_role_arn = aws_iam_role.ecs_task_execution_role.arn

  depends_on = [
    aws_cloudwatch_log_group.task_log_group,
    aws_iam_role_policy_attachment.ecs_role_policy_attachment
  ]
  tags = {
      Application = "backend-task-definition"
  }
}


resource "aws_security_group" "ecs_service_sg" {
  name        = "ecs_service_sg"
  description = "Security group for the ECS service"
  vpc_id      = var.vpc_id

  tags = {
    Name = "ecs-service-sg"
  }
}

resource "aws_vpc_security_group_ingress_rule" "ingress_from_alb" {
  security_group_id = aws_security_group.ecs_service_sg.id
  from_port         = 8000
  to_port         = 8000
  ip_protocol       = "tcp"
  referenced_security_group_id  = var.alb_sg_id

  description = "Allow connections from the ALB"
}

resource "aws_vpc_security_group_egress_rule" "egress_all" {
  security_group_id = aws_security_group.ecs_service_sg.id
  ip_protocol        = "-1"
  cidr_ipv4          = "0.0.0.0/0"

  description = "Allow all outbound traffic"
}

resource "aws_ecs_service" "backend" {
  name = "backend"
  cluster = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.backend.arn
  desired_count = 2
  launch_type = "FARGATE"

  network_configuration {
    subnets = var.private_subnets_ids
    security_groups = [aws_security_group.ecs_service_sg.id]
  }

  load_balancer {
    target_group_arn = var.backend_tg_arn
    container_name   = "api-nginx"
    container_port   = 8000
  }
}