//
//  UITextField+BorderTextField.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 15/07/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UITextField (BorderTextField) <UITextFieldDelegate>
- (CGRect)textRectForBounds:(CGRect)bounds;
- (CGRect)editingRectForBounds:(CGRect)bounds;


@end
