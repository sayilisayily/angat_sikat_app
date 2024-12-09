#include <LiquidCrystal_I2C.h>
LiquidCrystal_I2C lcd(0x27, 20, 2); // set the LCD address to 0x27 for a 16 chars and 2 line display

#define _1PHP 3
#define _5PHP 4
#define _10PHP 5

 
int count_1PHP = 0;
int count_5PHP = 0;
int count_10PHP = 0;

 
int curr_state_1PHP = LOW;
int prev_state_1PHP = HIGH;
int curr_state_5PHP = LOW;
int prev_state_5PHP = HIGH;
int curr_state_10PHP = LOW;
int prev_state_10PHP = HIGH;

 
void intro() {
 lcd.setCursor(2, 0);
 lcd.print("COIN SORTING");
 lcd.setCursor(3, 1);
 lcd.print("GROUP 7");
 delay(3000);
 lcd.clear();
}
 
void setup() {
 lcd.init();
 lcd.backlight();
 intro();
 lcd.setCursor(0, 0);
 lcd.print("1  5  10 PHP");
}
 
void loop() {
 lcd.setCursor(0, 1);
 lcd.print(count_1PHP);
 lcd.setCursor(3, 1);
 lcd.print(count_5PHP);
 lcd.setCursor(7, 1);
 lcd.print(count_10PHP);
 
 
 curr_state_1PHP = digitalRead(_1PHP);
 curr_state_5PHP = digitalRead(_5PHP);
 curr_state_10PHP = digitalRead(_10PHP);

 
 if (curr_state_1PHP == LOW && prev_state_1PHP == HIGH) {
 count_1PHP++;
 }
 if (curr_state_5PHP == LOW && prev_state_5PHP == HIGH) {
 count_5PHP++;
 }
 if (curr_state_10PHP == LOW && prev_state_10PHP == HIGH) {
 count_10PHP++;
 }
 
 
 prev_state_1PHP = curr_state_1PHP;
 prev_state_5PHP = curr_state_5PHP;
 prev_state_10PHP = curr_state_10PHP;
}
