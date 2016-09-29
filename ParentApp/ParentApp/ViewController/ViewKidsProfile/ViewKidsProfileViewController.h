//
//  ViewKidsProfileViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "AsyncImageView.h"
#import "Children.h"
@interface ViewKidsProfileViewController : AppBaseViewController
{
    Children *childrenInfo;
    __weak IBOutlet UIView *view_childProfile;
    __weak IBOutlet UIScrollView *scrollView;
    __weak IBOutlet AsyncImageView *view_profileImage;
    __weak IBOutlet UILabel *lbl_favBook;
    __weak IBOutlet UILabel *lbl_favCartoon;
    __weak IBOutlet UILabel *lbl_favFood;
    __weak IBOutlet UITextView *txtView_helpfullHint;
    __weak IBOutlet UILabel *lbl_medication;
    __weak IBOutlet UILabel *lbl_alergies;
    __weak IBOutlet UILabel *lbl_specialNeeds;
    __weak IBOutlet UILabel *lbl_sex;
    __weak IBOutlet UIImageView *img_childProfile;
    __weak IBOutlet UILabel *lbl_kidsName;
    __weak IBOutlet UILabel *lbl_age;
    NSMutableArray *array_childrenDetail;
    NSMutableDictionary *dict_childData;
    NSMutableArray *array_childData;
    int pageNo;
    int totalPage,currentPage;
    
}
@property(nonatomic,strong)NSMutableDictionary *dict_childRecord;
@property(nonatomic,assign)Parent *parentInfo;
- (IBAction)onClickEditChildProfile:(id)sender;
- (IBAction)onClickAddChild:(id)sender;

@end
