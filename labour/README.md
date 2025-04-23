# Project README

## Want to try it out?

Steps:
1. Clone this repo to wherever you'd like (assuming you have git installed)
2. Open a terminal and navigate to where you cloned the repo
3. Run the command `docker-compose up -d --build` (assuming you have docker installed)
4. Head to http://localhost:8080/ to check out the website! Can also go to http://localhost:5000/ to use phpmyadmin

### Want to run the tests?

Steps:
1. Assuming you have already navigated to where you have clones the repo, run the command `docker build . -t phptest`
2. Next, to actually run the tests, run the command `docker run --network=project-2-manpower-scheduling-board-project-2-manpower-scheduling-board_default --rm -v //$(pwd):/app jitesoft/phpunit phpunit ./tests --testdox`


### Having issues?

Restart the website by doing `docker-compose down -v` and then run `docker-compose up -d` again

## Project Purpose

The purpose of this project is to develop a manageable chart for Horizon Electric Inc., a local Electrical Contractor, to maintain and provide outlook for future manpower requirements. The project aims to improve Horizon Electric Inc.'s manpower scheduling and planning processes, ultimately increasing their productivity and efficiency.

## Team Members

- Adam Fipke
- Jordan Roberts
- Eddy Zhang
- Edwin Zhou
- Yuan Zhu

## High-level Project Description

The project involves digitizing Horizon Electric's existing employee "whiteboard" and creating a user-friendly interface to easily reschedule employees by dragging and dropping their names. It will also include a manually editable forecast for future month employee counts. The project must be able to run constantly on a locally hosted server within a Docker container.

## Summary Milestone Schedule

- May 30: Development start
- June 25: 50% completion of the project
- July 20: At least 80% complete (Start review, refactoring, and polishing software)
- Aug 6: Aim to have 100% complete and deliver the product to the client.
- Aug 15: When it was actually finished lol

## Stakeholders

- Horizon Electric Inc. (Client)
  - Dave Clark (Contact person)
- Project team
- Horizon Electric Inc. employees
- University of British Columbia Okanagan (Contract provider)
- Clients of Horizon Electric